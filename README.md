## Nex
A simple Multi-Threaded HTTP server in python using socket programming

[![Build Status](https://travis-ci.com/pinkeshbadjatiya/Nex.svg?token=qJ4qdp1jw54BTny2oTYq&branch=master)](https://travis-ci.com/pinkeshbadjatiya/Nex)

### Instructions
- `settings.conf` file contains all the settings related to the server. Change them as per your need before running the server
- Run `python server.py` to initialize the server.
- `tests/` directory includes all the sample files that are used for nose2-testing.
- `public_html` is the server directory that is used by the server to host the codes.
- `error_pages` contains all the templates of various error codes.
- `other_templates` contains other templates used for rendering by the Nex server.

### Notes
- Do not add trailing '/' in PUBLIC_HTML and ERROR_DIR or any other directory in settings.conf
- All the essential directories must be present in the appropriate location as mentioned in the configuration file.

### Features Supported
- Proper status_codes if file found or not ...
- Mimetype taken into account of returned file
- Variable injection in template
- Listing contents of Directory with details about the files.
- HOST validation
- 403 forbidden, 404 not found etc error codes handled
- Separate configuration file for server  
- Client mapping with IPv4 address
- Threading between clients and locks and complete private data(using local variables for each thread)
- Tests written using nose2 and integrated in Travis-CI
- Thread safe logging
- Audio/Video/Image rendering
- URL encoding/decoding
- py2 and py3 support (partial support for py3 for now)

### TODO
- Tests for all the errors returned
- Checking the presence of all the configuration/essential directories before forking the server thread.
- Separate error pages from actual functions and break the flow if error to respond error funcs.
- Split server class into multiple ones.(PEP8)
- Line 255  - server.py
- Line 82 - server.py
- Respect more headers
- Convert this server to a proxy server, which can be toggled by the switch of a button. This requires a lot of work, but is doable.
