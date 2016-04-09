import utils
import operator
import json
import os
import nose2
import re

TEST_DIRECTORY = "./sample_test_files/"
CURRENT_DIRECTORY = "./"
IP_REGEX = "^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$"

def test_loadConfig():
    expected = '{"STATUS_STRING": {"200": "200 OK", "404": "404 Not Found", "403": "403 Forbidden", "500": "500 Server Error"}, "MAX_CLIENT_QUEUE": 5, "SERVER_NAME": "Nex - Simple HTTP server/v.0.2", "HOST_ALLOWED": ["*"], "BIND_PORT": 12345, "ERROR_DIR": "./error_pages", "MAX_REQUEST_LEN": 1024, "SERVER_SHORT_NAME": "Nex/v.0.2", "HOST_NAME": "localhost", "PUBLIC_HTML": "./public_html", "OTHER_TEMPLATES": "./other_templates"}'
    config = utils.loadConfig(TEST_DIRECTORY + 'settings.conf')
    assert json.dumps(config) == expected, "loadConfig: Failed to load the configFile."

def test_directory_check():
    config = utils.loadConfig(CURRENT_DIRECTORY + 'settings.conf')

    # Check for PUBLIC_HTML, ERROR_DIR, OTHER_TEMPLATES
    for key in ['PUBLIC_HTML', 'ERROR_DIR', 'OTHER_TEMPLATES']:
        config[key] = config[key].strip(" ")
        assert config[key]!="", key + ": Field cannot be left blank"
        assert config[key][-1]!="/", key + ": Directory name must not end with a '/'"


def test_valid_hostname():
    config = utils.loadConfig(CURRENT_DIRECTORY + 'settings.conf')
    hostname = config['HOST_NAME']
    if len(hostname) > 15:
        raise AssertionError('HOST_NAME: hostname cannot be more than 15 character long.')

    try:
        assert hostname == "localhost", "HOST_NAME: Invalid Host Name"
        return
    except:
        allowed = re.compile(IP_REGEX, re.IGNORECASE)
        assert allowed.match(hostname), "HOST_NAME: Invalid Host Name"


if __name__ == "__main__":
    os.system("nose2")
