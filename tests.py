import utils
import operator
import json
import os
import nose.tools

TEST_DIRECTORY="./sample_test_files/"

def test_loadConfig():
    config = utils.loadConfig(TEST_DIRECTORY + 'settings.conf')
    nose.tools.eq_(json.dumps(config), '{"STATUS_STRING": {"200": "200 OK", "404": "404 Not Found", "403": "403 Forbidden", "500": "500 Server Error"}, "MAX_CLIENT_QUEUE": 5, "SERVER_NAME": "Nex - Simple HTTP server/v.0.2", "HOST_ALLOWED": ["*"], "BIND_PORT": 12345, "ERROR_DIR": "./error_pages", "MAX_REQUEST_LEN": 1024, "SERVER_SHORT_NAME": "Nex/v.0.2", "HOST_NAME": "localhost", "PUBLIC_HTML": "./public_html", "OTHER_TEMPLATES": "./other_templates"}')


if __name__ == "__main__":
    os.system("nosetests --verbosity=3 -x .")
