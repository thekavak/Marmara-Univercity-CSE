import datetime
import json
import socket
# our custom module
import error_codes as ourmodule



def getName(query_string):
    key, value = query_string.split('=')
    return value

def process_request(request):
    print("Received: %s" % request)
    lines = request.split('\n')
    method, path, _ = lines[0].split()
    path, query_string = path.split('?', 1)
    query_params = {}
    if method == 'GET' or method == 'POST':
        #room add
        if path == '/add' :
            name = getName(query_string)
             # check if activity exists in json
            if room_exists_in_json(name):
                # return error message
                return make_return_message(ourmodule.http_403,str('Error'),'Room already exists in database!') 
                # add activity to json
            add_room_to_reservations_system(name)

            # return success message
            return make_return_message(ourmodule.http_200,'Room Added!',f'Room with name {name} is successfully added.') 
        #room remove
        elif path == '/remove':

            name = getName(query_string)
            # check if activity exists in json
            if room_exists_in_json(name):
                # remove activity from json
                remove_room_from_json(name)
                # return success message
                return make_return_message(ourmodule.http_200,'Room Removed!',f'Room with name {name} is successfully removed.')
            else:
                # return error message
             return make_return_message(ourmodule.http_403,'Error','Room does not exist!')
         #room reserve
        elif path == '/reserve':
            #check parameters are exist
            if query_string.find('day') != -1 and query_string.find('hour') != -1 and query_string.find('duration') != -1:
                params = query_string.split('&')
                for param in params:
                    key, value = param.split('=')
                    query_params[key] = value

                name = query_params['name']
                day = int(query_params['day'])
                start_time = int(query_params['hour'])
                end_time = start_time + int(query_params['duration'])
            else:
                #return error message
                return make_return_message(ourmodule.http_400,str('Error'),'Invalid parameters!')
            #check room exists
            if room_exists_in_json(name):
                #make reservation
                return reserve_room_in_json(name, day, start_time, end_time)

            else:
                #return error message
                return make_return_message(ourmodule.http_403,str('Error'),f'Room with name {name}does not exist!')
        #room availability
        elif path == '/checkavailability':
            #check parameters are exist
            if query_string.find('name') != -1 and query_string.find('day') != -1:
                params = query_string.split('&')
                for param in params:
                    key, value = param.split('=')
                    query_params[key] = value

                name = query_params['name']
                day = int(query_params['day'])
            else:
                #return error message
                return make_return_message(ourmodule.http_400,str('Error'),'Invalid parameters!')
            #check day is valid or not
            if day < 1 or day > 7:
                    return make_return_message(ourmodule.http_400,str('Error'), 'Day parameter is invalid!')

            if room_exists_in_json(name):
                # Open the JSON file for reading
                        with open("rooms.json", "r") as f:
                            data = json.load(f)
                            schedule = data[name]
                            hours = schedule[str(day)]
                            available_hours = []
                            # Check if the hour is available
                            for hour in range(9, 18):
                                hour_available = True
                                for reservation in hours:
                                 if reservation["start_time"] <= hour < reservation["end_time"]:
                                    hour_available = False
                                    break
                                # Add the hour to the list of available hours if it is available
                                if hour_available:
                                    available_hours.append(hour)
                            # Return the list of available hours
                            return make_return_message(ourmodule.http_200,'Room Available',f'Room {name} is available for the following hours: {available_hours}')
            else:
                #return error message
                return make_return_message(ourmodule.http_404,'Not Found','Room does not exist!')
        else:
            #return error message
            return make_return_message(ourmodule.http_404,'Not Found','Invalid path!')
    # handle other HTTP methods
    else:
        return make_return_message(ourmodule.http_405,str('Error'),'Method Not Allowed')


def reserve_room_in_json(name, day, start_time, end_time):
    # Check if the parameters are valid
    if day < 1 or day > 7:
        return make_return_message(ourmodule.http_400,str('Error'), 'Day parameter is invalid!')
        # Check if the time range is valid
    if start_time < 9 or start_time > 17:
        return make_return_message(ourmodule.http_400,str('Error'),'Hour parameter is invalid!')
    if end_time > 17:
        return make_return_message(ourmodule.http_400,str('Error'),'Duration parameter is invalid!')

    with open('rooms.json', 'r') as f:
        room_availability = json.load(f)
        schedule = room_availability[str(name)]
        hours = schedule[str(day)]
        # Check if the time range is available
        if not any(start_time <= hour_["start_time"] <  end_time or start_time <= hour_["end_time"] < end_time for hour_ in hours):
            # Generate a unique reservation ID
            
            # Add the reservation to the schedule
            hours.append({"start_time": start_time, "end_time": end_time, "id": 1})
            with open("rooms.json", "w") as f:
                json.dump(room_availability, f, indent=2)
            return make_return_message(ourmodule.http_200,'Room Reserved!',f'Room {name} is reserved for {start_time} to {end_time} on day {ourmodule.weekdays[day-1]}.')

        else:
            return make_return_message(ourmodule.http_403,str('Error'),'Room already reserved! ')

  

# Check if a room exists in the JSON file
def room_exists_in_json(name):
    with open('rooms.json', 'r') as f:
        data = json.load(f)
    return name in data
 # Remove a room from the JSON file  
def remove_room_from_json(name):
    with open('rooms.json', 'r') as f:
        data = json.load(f)
        del data[name]
    with open('rooms.json', 'w') as f:
        json.dump(data, f)

        
# Add a room to the JSON file
def add_room_to_reservations_system(name):
    # Create a new room
    new_room = name
    new_room_days = {
        1: [],  # Monday, no reservations
        2: [],  # Tuesday, no reservations
        3: [],  # Wednesday, no reservations
        4: [],  # Thursday, no reservations
        5: [], 
        6: [],  
        7: [],  
    }
    with open("rooms.json", "r") as f:
        database = json.load(f)
        database[new_room] = new_room_days
    with open("rooms.json", "w") as f:
        json.dump(database, f)

    with open("reservations.json", "r") as f2:
        database2 = json.load(f2)
        database2[new_room] = new_room_days
    with open("reservations.json", "w") as f2:
        json.dump(database2, f2)    


def make_return_message(status_code,title,message):
    return ourmodule.make_return_message(status_code,title,message)





# create a socket object
server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

# get local machine name
host = ''
port = 8089
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
   

        