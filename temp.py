WWW_Dir = "/var/www/html"

string = bytes.decode(data) #decode it to string
request_method = string.split(' ')[0]       #determine request method  (HEAD and GET are supported)
print ("Method: ", request_method)
print ("Request body: ", string)
if (request_method == 'GET') | (request_method == 'HEAD'):
    file_requested = string.split(' ')
    file_requested = file_requested[1] # get 2nd element   # split on space "GET /file.html" -into-> ('GET','file.html',...)
#Check for URL arguments. Disregard them
file_requested = file_requested.split('?')[0]  # disregard anything after '?'
if (file_requested == '/'):  # in case no file is specified by the browser
    file_requested = '/index.html' # load index.html by default
file_requested = WWW_Dir + file_requested
print ("Serving web page [",file_requested,"]")

## Load file content
try:
    file_handler = open(file_requested,'rb')
    if (request_method == 'GET'):  #only read the file when GET
        response_content = file_handler.read() # read file content
    file_handler.close()
    response_headers = createResponse(200, response_content)
except Exception as e: #in case file was not found, generate 404 page
    print ("Warning, file not found. Serving response code 404\n", e)
    response_headers = self._gen_headers( 404)
    if (request_method == 'GET'):
        response_content = b"<html><body><p>Error 404: File not found</p><p>Python HTTP er</p></body></html>"
    server_response =  response_headers.encode() # return headers for GET and HEAD
    if (request_method == 'GET'):
        server_response +=  response_content  # return additional conten for GET only
    conn.send(server_response)
    print ("Closing connection with client")
    conn.close()
else:
    print("Unknown HTTP request method:", request_method)
