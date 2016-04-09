
from __future__ import absolute_import, division, print_function
from builtins import *

import sys
import json
from http.server import BaseHTTPRequestHandler
from io import StringIO


def sizeof_fmt(num, suffix='B'):
    for unit in ['','K','M','G','T','P','E','Z']:
        if abs(num) < 1000.0:
            return "%3.1f %s%s" % (num, unit, suffix)
        num /= 1000.0
    return "%.1f%s%s" % (num, 'Yb', suffix)


def loadConfig(fileName):
    try:
        with open(fileName,'r') as d:
            return json.load(d)
    except IOError:
        print("Error: File does not appear to exist.")
        sys.exit(1)
    except ValueError:
        print("Error: Config file format not correct.")
        sys.exit(1)
    except:
        print("Error: Something went wrong trying to load the settings.")
        sys.exit(1)


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
