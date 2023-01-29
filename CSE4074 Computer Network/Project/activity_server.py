import datetime
import json
import socket
# our custom module
import error_codes as ourmodule



    
def process_request(request):
 
    lines = request.split('\n')
    method, path, _ = lines[0].split()
    path, query_string = path.split('?', 1)
    query_params = {}
    key, value = query_string.split('=')
    query_params[key] = value

    # get name from query params
    name = query_params['name']
   
    # check if method is GET or POST
    if method == 'GET' or method == 'POST':
       # check if path is /add
        if path == '/add' :
            # check if activity exists in json
            if activity_exists_in_json(name) is True:
                # return error message
                return make_return_message(ourmodule.http_403,'Error','Activity already exists in database!')
            else:
                # add activity to json
                add_activity(name)
                # return success message
                return make_return_message(ourmodule.http_200,'Activity Added!',f'Activity with name {name} is successfully added.')
        # check if path is /remove
        elif path == '/remove':
            # check if activity exists in json
            if activity_exists_in_json(name) is True:
                # remove activity from json
                remove_activity_from_json_list(name)
                # return success message
                return make_return_message(ourmodule.http_200,'Activity Removed!',f'Activity with name {name} is successfully removed.')
            else:
                # return error message
             return make_return_message(ourmodule.http_403,'Error','Activity does not exist!')
       # check if path is /check
        elif path == '/check':
            # check if activity exists in json
            if activity_exists_in_json(name) is True:
                # return success message
              return make_return_message(ourmodule.http_200,'Activity Exists!',f'Activity with name {name} exists.')
            else:
                # return error message
                return make_return_message(ourmodule.http_403,'Error',f'Activity with name {name} does not exists.')
    # handle other HTTP methods
    else:
        return make_return_message(ourmodule.http_405,'Error','Method Not Allowed')



# check if activity exists in json
def activity_exists_in_json(name):
    with open('activities.json', 'r') as f:
        data = json.load(f)
        activities = data['activities']
    if name in activities:
        return True
# remove activity from json list
def remove_activity_from_json_list(name):
    with open("activities.json", "r") as f:
        database = json.load(f)
        database["activities"].remove(name)
    with open("activities.json", "w") as f:
        json.dump(database, f)
# add activity to json
def add_activity(name):
    with open("activities.json", "r") as f:
        database = json.load(f)
        database["activities"].append(name)
    with open("activities.json", "w") as f:
        json.dump(database, f)

# create a return message
def make_return_message(status_code,title,message):
    return ourmodule.make_return_message(status_code,title,message)

# create a socket object
server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

# get local machine name
host = ''
port = 8087
# bind to the port
server_socket.bind((host, port))
# queue up to 5 requests
server_socket.listen(5)

while True:
    # establish a connection
    client_socket, addr = server_socket.accept()
    print("Got a connection from %s" % str(addr))
    data = client_socket.recv(1024).decode()
    print("Received: %s" % data)
    # parse the incoming request to determine the HTTP method and URI
    response = process_request(data)
    client_socket.send(response.encode())
    client_socket.close()