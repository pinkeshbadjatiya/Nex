#! /usr/bin/python

from __future__ import absolute_import, division, print_function
from builtins import *

import sys
import json
from http.server import BaseHTTPRequestHandler
from io import StringIO
import os
import ColorizePython
import mimetypes


class HTTPRequest(BaseHTTPRequestHandler):
    def __init__(self, request_text):
        # request_text = str(request_text).encode('utf-')
        request_text = request_text.decode('utf-8')
        self.rfile = StringIO(request_text)
        self.raw_requestline = self.rfile.readline()
        self.error_code = self.error_message = None
        self.parse_request()

    def send_error(self, code, message):
        self.error_code = code
        self.error_message = message


def sizeof_fmt(num, suffix='B'):
    for unit in ['','K','M','G','T','P','E','Z']:
        if abs(num) < 1000.0:
            return "%3.1f %s%s" % (num, unit, suffix)
        num /= 1000.0
    return "%.1f%s%s" % (num, 'Yb', suffix)


def loadConfig(fileName):
    try:
        with open(fileName,'r') as d:
            jsn = json.load(d)
            jsn['PUBLIC_HTML'] = os.path.normpath(jsn['PUBLIC_HTML'])   # Normalize path
            jsn['ERROR_DIR'] = os.path.normpath(jsn['ERROR_DIR'])   # Normalize path
            jsn['OTHER_TEMPLATES'] = os.path.normpath(jsn['OTHER_TEMPLATES'])   # Normalize path
            return jsn
    except IOError:
        print("Error: File does not appear to exist.")
        sys.exit(1)
    except ValueError:
        print("Error: Config file format not correct.")
        sys.exit(1)
    except:
        print("Error: Something went wrong trying to load the settings.")
        sys.exit(1)


def colorizeLog(shouldColorize, log_level, msg):
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


def guessMIME(filename):
    return mimetypes.guess_type(filename)[0]


def isvalidPath(location):
    """ Check if file/directory exists """
    if os.path.exists(location):
        return True
    return False


def isvalidFile(location):
    """ Check if file exists """
    if isvalidPath(location) and os.path.isfile(location):
        return True
    return False


def isvalidDirectory(location):
    """ Check if directory exists """
    if (isvalidPath(location)) and (not os.path.isfile(location)):
        return True
    return False


def isReadable(location):
    """ Check if file/directory is readable """
    if os.access(location, os.R_OK):
        return True
    return False
