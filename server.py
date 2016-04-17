#! /usr/bin/python

from __future__ import absolute_import, division, print_function
from builtins import *

import socket
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
import threading
import logging
import urllib
import binascii
from io import StringIO

import ColorizePython
from utils import *

logging.basicConfig(level=logging.DEBUG,
                    format='[%(CurrentTime)-10s] (%(ThreadName)-10s) %(message)s',
                    )


class Server:
    """ The server class """

    def __init__(self, config):
        signal.signal(signal.SIGINT, self.shutdown)  # Shutdown on Ctrl+C
        self.config = config                                         # Save config in server
        self.serverSocket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)             # Create a TCP socket
        self.serverSocket.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)    # Re-use the socket
        self.serverSocket.bind((self.config['HOST_NAME'], self.config['BIND_PORT'])) # bind the socket to a public host, and a port
        self.serverSocket.listen(self.config['MAX_CLIENT_BACKLOG'])    # become a server socket
        self.__clients = {}
        self.__client_no = 1


    def listenForClient(self):
        """ Wait for clients to connect """
        local_data = threading.local()
        while True:
            self.log("SUCCESS", -1, 'Ready to serve...')
            (clientSocket, client_address) = self.serverSocket.accept()   # Establish the connection
            if self.config['PROXY_SERVER'].lower() == "true":
                d = threading.Thread(name=self._getClientName(client_address), target=self.proxy_thread, args=(clientSocket, client_address))
            else:
                d = threading.Thread(name=self._getClientName(client_address), target=self.handleClient, args=(clientSocket, client_address, local_data))
            d.setDaemon(True)
            d.start()
        self.shutdown(0,0)

    def _getClientName(self, cli_addr):
        """ Return the clientName with appropriate number.
        If already an old client then get the no from map, else
        assign a new number.
        """
        lock = threading.Lock()
        lock.acquire()
        ClientAddr = cli_addr[0]
        if ClientAddr in self.__clients:
            lock.release()
            return "Client-" + str(self.__clients[ClientAddr])

        self.__clients[ClientAddr] = self.__client_no
        self.__client_no += 1
        lock.release()
        return "Client-" + str(self.__clients[ClientAddr])

    def handleClient(self, clientSocket, client_address, local):
        """ Manage the client which got connected. Has to be done parallely.
        Use "local" as prefix for all the temporary variables. These are thread safe.
        """
        try:
            self.log("NORMAL", client_address, 'Connection from: ' + str(client_address))
            clientSocket.settimeout(self.config['CONNECTION_TIMEOUT'])
            local.data = clientSocket.recv(self.config['MAX_REQUEST_LEN'])
            if local.data == "":  # Ignore the blank requests
                return
            self.log("NORMAL", client_address, 'Sending data back to the client')
            clientSocket.sendall(self.createResponse(self.parseRequest(client_address, local.data)))
        finally:
            clientSocket.close()         # Clean up the connection



    def createResponse(self, content, response_code=200, mimetype='text/html', encoding='UTF-8', additional_params=None):#, last_modified=0):
        """
            Create the response from the STATUS_CODE, DATA and MIMETYPE received.
            Receives    => (content, 200, text/html, UTF-8, additionalParamsDict)
            Returns     => consists of header + content.
        """

        if len(content) >= 3:
            mimetype = content[2]
        if len(content) >= 2:
            response_code = content[1]

        header_params = {
            'Content-Type': '%s; charset=%s' %(mimetype, encoding) if encoding else mimetype,
            'Date': strftime("%a, %d %b %Y %X GMT", gmtime()),
            'Server': self.config['SERVER_NAME'],
            # 'Connection': 'close',
            'Connection' : 'close',  # signal that the conection wil be closed after completing the request
            'Content-Length': len(content[0]),
            # 'Keep-Alive': 'timeout=5, max=100',
            # 'Etag': "ae7b5b-52dca51ae0420"',
            # 'Accept-Ranges': "bytes",
        }

        if len(content) >= 4:
            for k, v in additional_params.iteritems():
                header_params[k] = v
        content = content[0]

        # if encoding:
            # content = content.encode(encoding)

        header = "HTTP/1.0 %s\r\n%s\r\n" % (
            self.config['STATUS_STRING'][str(response_code)],
            ''.join('%s: %s\r\n' % kv for kv in header_params.iteritems())
        )
        return header.encode('utf8') + content


    def parseRequest(self, client_address, data):
        """ Parses the request and returns the error code and body content.
            Returns     => content, response_code
        """
        request = HTTPRequest(data)
        request.path = url=urllib.unquote(request.path).decode('utf8')

        if request.error_code != None:
            return request.error_message, request.error_code
        if not self._ishostAllowed(request.headers['host']):
            return self._readFile(self.config['ERROR_DIR'] + '/' + str(403) + ".html"), 403

        if request.command == "GET":
            return self._handleGET(client_address, request.path)
        else:
            return self._readFile(self.config['ERROR_DIR'] + '/' + str(500) + ".html"), 500


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

        self.log("NORMAL", client_address, path)
        filepath = self.config['PUBLIC_HTML'] + path

        # For both directory and files, check if path exists
        if not os.path.exists(filepath):
            return self._readFile(self.config['ERROR_DIR'] + '/' + str(404) + ".html"), 404

        # Check if read permission
        if not (os.access(filepath, os.R_OK) and filepath.startswith(self.config['PUBLIC_HTML'])):
            return self._readFile(self.config['ERROR_DIR'] + '/' + str(403) + ".html"), 403

        # Check if directory but path exists
        if not os.path.isfile(filepath):
            return self._handleDirectory(filepath)

        # File exists and read permission, so give file
        try:
            fp = open(filepath, "rb")
        except IOError as e:
            if e.errno == errno.EACCES:
                return self._readFile(self.config['ERROR_DIR'] + '/' + str(500) + ".html"), 500
            # Not a permission error.
            raise
        else:
            with fp:
                # >> return data, 200, mimetype
                return fp.read(), 200, self._guessMIME(filepath)


    def _handleDirectory(self, dirname):
        """ Create a HTML page using template injection and render a tablular view of the directory. """

        entry = "<tr><td>[{{-EXTENSION-}}]</td><td><a href='{{-HREF-}}'>{{-FILE_NAME-}}</a></td><td align='right'>{{-DATE_MODIFIED-}}</td><td align='right'>{{-FILE_SIZE-}}</td></tr>"

        dirname = dirname.strip("/")      # Remove trailiing/ending back-slashes...

        all_entries = ""
        template = self._readFile(self.config['OTHER_TEMPLATES'] + '/' + "dir.html")
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

        return self._inject_variables(template, dicto), 200


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
                return self._readFile(self.config['ERROR_DIR'] + '/' + str(500) + ".html")
            # Not a permission error.
            raise
        else:
            with fp:
                return fp.read()     # return (data, mimetype)


    def _guessMIME(self, filename):
        return mimetypes.guess_type(filename)[0]


    def _toHREF(self, path):
        """ Return relative path (from public_html) from absolute path """
        return path.split(self.config['PUBLIC_HTML'])[-1]


    def log(self, log_level, client, msg):
        """ Log the messages to appropriate place """
        LoggerDict = {
            'CurrentTime' : strftime("%a, %d %b %Y %X", localtime()),
            'ThreadName' : threading.currentThread().getName()
        }
        if client == -1:       # Main Thread
            formatedMSG = msg
        else:                  # Child threads or Request Threads
            formatedMSG = '{0}:{1} {2}'.format(client[0], client[1], msg)
        logging.debug('%s', self.colorizeLog(self.config['COLORED_LOGGING'], log_level, formatedMSG), extra=LoggerDict)


    def colorizeLog(self, shouldColorize, log_level, msg):
        ## Higher is the log_level in the log() argument, the lower is its priority.
        colorize_log = {
            "NORMAL": ColorizePython.pycolors.ENDC,
            "WARNING": ColorizePython.pycolors.WARNING,
            "SUCCESS": ColorizePython.pycolors.OKGREEN,
            "FAIL": ColorizePython.pycolors.FAIL,
            "RESET": ColorizePython.pycolors.ENDC
        }

        if shouldColorize.lower() == "true":
            if log_level in colorize_log:
                return colorize_log[str(log_level)] + msg + colorize_log['RESET']
            return colorize_log["NORMAL"] + msg + colorize_log["RESET"]
        return msg


    def shutdown(self, signum, frame):
        """ Handle the exiting server. Clean all traces """

        self.log("WARNING", -1, 'Shutting down gracefully...')
        main_thread = threading.currentThread()        # Wait for all clients to exit
        for t in threading.enumerate():
            if t is main_thread:
                continue
            self.log("FAIL", -1, 'joining ' + t.getName())
            t.join()
        self.serverSocket.close()
        sys.exit(0)


    def printout(self, type, request, address):
        colornum = "\033[96m"
        if "Block" in type or "Blacklist" in type:
            colornum = "\033[91m"
        elif "Request" in type:
            colornum = "\033[92m"
        elif "Reset" in type:
            colornum = "\033[93m"
        print(colornum, address[0],"\t",type,"\t",request,"\033[0m")


    def proxy_thread(self, conn, client_addr):
        """
        *******************************************
        *********** PROXY_THREAD FUNC *************
          A thread to handle request from browser
        *******************************************
        """

        request = conn.recv(self.config['MAX_REQUEST_LEN'])   # get the request from browser
        first_line = request.split('\n')[0]                   # parse the first line
        url = first_line.split(' ')[1]                        # get url

        # Check if the host:port is blacklisted
        for i in range(0,len(self.config['BLACKLIST_DOMAINS'])):
            if self.config['BLACKLIST_DOMAINS'][i] in url:
                self.log("FAIL", client_addr, "BLACKLISTED: " + first_line)
                conn.close()
                return

        self.log("WARNING", client_addr, "REQUEST: " + first_line)

        # find the webserver and port
        http_pos = url.find("://")          # find pos of ://
        if (http_pos==-1):
            temp = url
        else:
            temp = url[(http_pos+3):]       # get the rest of url

        port_pos = temp.find(":")           # find the port pos (if any)

        # find end of web server
        webserver_pos = temp.find("/")
        if webserver_pos == -1:
            webserver_pos = len(temp)

        webserver = ""
        port = -1
        if (port_pos==-1 or webserver_pos < port_pos):      # default port
            port = 80
            webserver = temp[:webserver_pos]
        else:                                               # specific port
            port = int((temp[(port_pos+1):])[:webserver_pos-port_pos-1])
            webserver = temp[:port_pos]

        try:
            # create a socket to connect to the web server
            s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            s.settimeout(self.config['CONNECTION_TIMEOUT'])
            s.connect((webserver, port))
            s.sendall(request)                           # send request to webserver

            while 1:
                data = s.recv(self.config['MAX_REQUEST_LEN'])          # receive dataprintout from web server
                if (len(data) > 0):
                    conn.send(data)                   # send to browser
                else:
                    break
            s.close()
            conn.close()
        except socket.error as error_msg:
            self.log("ERROR", client_addr, error_msg)
            if s:
                s.close()
            if conn:
                conn.close()
            self.log("WARNING", client_addr, "Peer Reset: " + first_line)
            # self.printout("Peer Reset", first_line, client_addr)


if __name__ == "__main__":
    config = loadConfig('settings.conf')
    server = Server(config)
    server.listenForClient()
