#! /usr/bin/python

from socket import *
from time import gmtime, strftime
import sys
import threading
import signal  # Signal support (server shutdown on signal receive)
import json
import fnmatch
import os
import errno
import mimetypes
from time import gmtime, strftime, localtime
from datetime import datetime
from utils import *


class Server:
    """ The server class """

    def __init__(self, config):
        signal.signal(signal.SIGINT, self.shutdown)  # Shutdown on Ctrl+C
        self.config = config                                         # Save config in server
        self.serverSocket = socket(AF_INET, SOCK_STREAM)             # Create a TCP socket
        self.serverSocket.setsockopt(SOL_SOCKET, SO_REUSEADDR, 1)    # Re-use the socket
        self.serverSocket.bind((self.config['HOST_NAME'], self.config['BIND_PORT'])) # bind the socket to a public host, and a port
        self.serverSocket.listen(self.config['MAX_CLIENT_QUEUE'])    # become a server socket

    def listenForClient(self):
        """ Wait for clients to connect """
        while True:
            self.log(-1, 'Ready to serve...')
            (clientSocket, client_address) = self.serverSocket.accept()   # Establish the connection
            self.handleClient(clientSocket, client_address)
        self.shutdown(0,0)


    def handleClient(self, clientSocket, client_address):
        """ Manage the client which got connected. Has to be done parallely """
        try:
            self.log(client_address, 'Connection from: ' + str(client_address))
            data = clientSocket.recv(self.config['MAX_REQUEST_LEN'])
            if data == "":  # Ignore the blank requests
                return
            self.log(client_address, 'Sending data back to the client')
            clientSocket.sendall(self.createResponse(self.parseRequest(client_address, data)))
        finally:
            clientSocket.close()         # Clean up the connection

    def createResponse(self, data):#, last_modified=0):
        """
            Create the response from the STATUS_CODE, DATA and MIMETYPE received.
            Response consists of header + content.
        """
        response_code = data[0]
        mimetype  = data[1][1]
        data = data[1][0]               # (200, (data, mimetype))

        res = "HTTP/1.0 " + self.config['STATUS_STRING'][str(response_code)] + "\r\n"
        res += "Content-Type: " + mimetype + "\r\n"
        res += "Date: " + strftime("%a, %d %b %Y %X GMT", gmtime()) + "\r\n"
        # if last_modified:
        #     res += "Last Modified: " + last_modified + "\r\n"
        res += 'Server: ' + self.config['SERVER_NAME'] + "\r\n"
        res += 'Connection: close' + '\r\n'  # signal that the conection wil be closed after complting the request
        res += "\r\n"
        res += data

        return res.encode("utf8")


    def parseRequest(self, client_address, data):
        """ Parses the request and returns the error code and body content.
        """
        request = HTTPRequest(data)
        if request.error_code != None:
            return (request.error_code, request.error_message)
        if not self._ishostAllowed(request.headers['host']):
            return (403, self._readFile(self.config['ERROR_DIR'] + '/' + str(403) + ".html"))

        if request.command == "GET":
            return self._handleGET(client_address, request.path)
        else:
            return (500, self._readFile(self.config['ERROR_DIR'] + '/' + str(500) + ".html"))


    def _ishostAllowed(self, host):
        """ Check if host is allowed to access the content """
        for wildcard in self.config['HOST_ALLOWED']:
            if fnmatch.fnmatch(host, wildcard):
                return True
        return False


    def _handleGET(self, client_address, path):
        """ Process the GET request of the client """
        # print request.command          # "GET"
        # print request.path             # "/who/ken/trust.html"
        # print request.request_version  # "HTTP/1.1"
        # print len(request.headers)     # 3
        # print request.headers.keys()   # ['accept-charset', 'host', 'accept']
        # print request.headers['host']  # "cm.bell-labs.com"

        self.log(client_address, path)
        filepath = self.config['PUBLIC_HTML'] + path

        # For both directory and files, check if path exists
        if not os.path.exists(filepath):
            return (404, self._readFile(self.config['ERROR_DIR'] + '/' + str(404) + ".html"))

        # Check if read permission
        if not (os.access(filepath, os.R_OK) and filepath.startswith(self.config['PUBLIC_HTML'])):
            return (403, self._readFile(self.config['ERROR_DIR'] + '/' + str(403) + ".html"))

        # Check if directory but path exists
        if not os.path.isfile(filepath):
            return self._handleDirectory(filepath)

        # File exists and read permission, so give file
        try:
            fp = open(filepath, "rb")
        except IOError as e:
            if e.errno == errno.EACCES:
                return (500, self._readFile(self.config['ERROR_DIR'] + '/' + str(500) + ".html"));
            # Not a permission error.
            raise
        else:
            with fp:
                # >> return (200,(data,mimetype))
                return (200, (fp.read().decode("utf8"), mimetypes.guess_type(filepath)[0]))


    def _handleDirectory(self, dirname):
        """ Create a HTML page using template injection and render a tablular view of the directory. """
        entry = "<tr><td>[{{-EXTENSION-}}]</td><td><a href='{{-HREF-}}'>{{-FILE_NAME-}}</a></td><td align='right'>{{-DATE_MODIFIED-}}</td><td align='right'>{{-FILE_SIZE-}}</td></tr>"

        dirname = dirname.strip("/")      # Remove trailiing/ending back-slashes...

        all_entries = ""
        template = self._readFile(self.config['OTHER_TEMPLATES'] + '/' + "dir.html")[0]
        for ent in os.listdir(dirname):
            variables = {
                'EXTENSION' : "DIR",
                'HREF' : self._toHREF(dirname + "/" + ent),
                'FILE_NAME' : ent,
                'DATE_MODIFIED' : datetime.fromtimestamp(os.stat(dirname + "/" + ent).st_mtime).strftime("%A %d, %B %Y, %H:%M:%S"),
                'FILE_SIZE' : "-"
            }

            # if the "ent" is a file
            if os.path.isfile(dirname + "/" + ent):
                if len(ent.split('.')) > 1:
                    variables['EXTENSION'] = ent.split('.')[-1]
                else:
                    variables['EXTENSION'] = "---"
                variables['FILE_SIZE'] = sizeof_fmt(os.stat(dirname + "/" + ent).st_size)

            all_entries += self._inject_variables(entry, variables)

        dicto = {
            'ENTRIES' : all_entries,
            'SERVER_DETAILS' : self.config['SERVER_SHORT_NAME']  + " Server at " + self.config['HOST_NAME'] + " Port " + str(self.config['BIND_PORT']),
            'PATH' : self._toHREF(dirname) + "/",
            'BACK_HREF' : "/".join((self._toHREF(dirname) + "/").split('/')[:-2])
        }
        if dicto['BACK_HREF'] == "":
            dicto['BACK_HREF'] = "/"

        return (200, (self._inject_variables(template, dicto), "text/html"))


    def _inject_variables(self, template, var_dict):
        """ Used to inject variables in the template """
        for key in var_dict:
            template = template.replace("{{-" + key + "-}}", var_dict[key])
        return template


    def _readFile(self, filename):
        # File exists and read permission
        try:
            fp = open(filename)
        except IOError as e:
            if e.errno == errno.EACCES:
                return self._readErrorFile(500);
            # Not a permission error.
            raise
        else:
            with fp:
                return (fp.read().decode('utf8'), mimetypes.guess_type(filename)[0])     # return (data, mimetype)

    def _toHREF(self, path):
        """ Return relative path (from public_html) from absolute path """
        return path.split(self.config['PUBLIC_HTML'])[-1]


    def log(self, client, msg):
        """ Log the messages to appropriate place """
        if client == -1:
            print >>sys.stderr, '[' + strftime("%a, %d %b %Y %X", localtime()) + '] %s' %  msg
        else:
            print >>sys.stderr, '[' + strftime("%a, %d %b %Y %X", localtime()) + '] %s:%s %s' % (client[0], client[1], msg)

    def shutdown(self, signum, frame):
        self.log(-1, 'Shutting down gracefully...')
        self.serverSocket.close()
        sys.exit(0)

if __name__ == "__main__":
    config = loadConfig('settings.conf')
    server = Server(config)
    server.listenForClient()
