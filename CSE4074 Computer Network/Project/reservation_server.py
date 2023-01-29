import json
import socket
# our custom module
import error_codes as ourmodule

#room server host and port
room_host = ''
room_port = 8089

#activity server host and port
activity_host = ''
activity_port = 8087


def process_request(request):
 
    lines = request.split('\n')
    method, path, _ = lines[0].split()
    path, query_string = path.split('?', 1)
    query_params = {}
   
    if method == 'GET' or method == 'POST':
       
        if path == '/listavailability':

            #check if quey string contains name and day
            if query_string.find('room') == -1:
                  return make_return_message(ourmodule.http_400,'Bad Request!','Invalid parameters!')
            elif query_string.find('room') != -1 and query_string.find('day') == -1:
                key, value = query_string.split('=')
                query_params[key] = value
                room = query_params['room']
                day = -1
            elif query_string.find('room') != -1 and query_string.find('day') != -1:
                params = query_string.split('&')
                for param in params:
                    key, value = param.split('=')
                    query_params[key] = value
                
                room = query_params['room']
                day = query_params['day']
            else:
                return make_return_message(ourmodule.http_400,'Bad Request!','Invalid parameters!')

            
            start = int(day) if int(day) != -1 else 1
            end = int(day)+1 if int(day) != -1 else 8
            avaliable_hours =''
            for day in range(start, end):
                room_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
                room_socket.connect((room_host, room_port))
                # connect to room server
                request = f"GET /checkavailability?name={room}&day={day} HTTP/1.1\r\n"
                room_socket.sendall(request.encode())
                room_socket.sendall(f"Host: localhost:{room_port}\r\n".encode()) 
                room_socket.sendall(b"\r\n")
                room_response = room_socket.recv(1024).decode()
                room_socket.close()
               
               # get status code
                status_code = get_status_code(room_response)
                # get data from response
                avaliable_hours += f'On {ourmodule.weekdays[day-1]}:'
                avaliable_hours += get_p_text(room_response);
                avaliable_hours += '\n'
    
              
            if status_code == 200:
                return make_return_message(ourmodule.http_200,'Available Hours',avaliable_hours)
            else:
                return make_return_message(str(status_code),get_title(room_response),get_p_text(room_response))
             
        elif path == '/reserve':
            #check if quey string contains name and day
            if query_string.find('room') == -1 or query_string.find('day') == -1 or query_string.find('hour') == -1 or  query_string.find('activity') == -1 or  query_string.find('duration') == -1:
                return make_return_message(ourmodule.http_400,'Bad Request!','Please enter all parameters!')
            else:
                # get query parameters
                params = query_string.split('&')
                for param in params:
                    key, value = param.split('=')
                    query_params[key] = value
                
                activity = query_params['activity']
                room = query_params['room']
                day = query_params['day']
                hour = query_params['hour']
                duration = query_params['duration']

                activity_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
                # sunucu adresi ve port numarası
                activity_socket.connect((activity_host, activity_port))
                #coonect to activity server
                request = f"GET /check?name={activity} HTTP/1.1\r\n"
                activity_socket.sendall(request.encode())
                activity_socket.sendall(f"Host: localhost:{activity_port}\r\n".encode())
                activity_socket.sendall(b"\r\n")

                activity_response = activity_socket.recv(1024).decode()
               
                activity_socket.close()

                status_code = get_status_code(activity_response)
                # check if activity exists
                if status_code == 200:
                    room_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
              
                    room_socket.connect((room_host, room_port))
                    # connect to room server
                    request = f"GET /reserve?name={room}&day={day}&hour={hour}&duration={duration} HTTP/1.1\r\n"
                    room_socket.sendall(request.encode())
                    room_socket.sendall(f"Host: localhost:8089\r\n".encode())
                    room_socket.sendall(b"\r\n")
                    # get response
                    room_response = room_socket.recv(1024).decode()
                 
                    room_socket.close()

                    # get status code
                    status_code = get_status_code(room_response)

                    if status_code == 200:
                          with open('reservations.json', 'r') as f:
                            rooms = json.load(f)
                            schedule = rooms[str(room)]
                            hours = schedule[str(day)]
                            # get max reservation id
                            max_id = 0
                            for activity_ in rooms:
                                for day in rooms[activity_]:
                                    for reservation in rooms[activity_][day]:
                                        if reservation['id'] > max_id:
                                            max_id = reservation['id']

                            # add new reservation
                            hours.append({ "id": int(max_id)+int(1),"activityname":activity,"start_time": int(hour), "end_time": int(hour)+int(duration)})
                            with open("reservations.json", "w") as f:
                                 json.dump(rooms, f, indent=2)
                            return make_return_message(ourmodule.http_200,'Reservation is successful!',f'You have reserved activity:{activity}, room: {activity_} for {duration} hours on {ourmodule.weekdays[int(day)-1]} at {hour}')
                    else:
                        return make_return_message(str(status_code),get_title(room_response),get_p_text(room_response))
                else:
                    return make_return_message(str(status_code),get_title(activity_response),get_p_text(activity_response))
       
        elif path == '/display':
            # check if id is given
            if query_string.find('id') == -1:
                return make_return_message(ourmodule.http_400,'Bad Request!','Please enter id parameters!')
            else:
                # get query parameters
                key, value = query_string.split('=')
                query_params[key] = value
                display_id = query_params['id']
                # get reservation details
                reservation = get_reservation_details(display_id)

            if reservation:
                return make_return_message(ourmodule.http_200,'Reservation Details',f'You have reserved Activity:{reservation["activityname"]} <br> room: {reservation["room"]}  <br> {reservation["end_time"]-reservation["start_time"]} hours on {ourmodule.weekdays[reservation["day"]]} at {reservation["start_time"]}:00')
            else:
                return make_return_message(ourmodule.http_404,'Not Found!','Reservation not found!')
    # handle other HTTP methods
    else:
        return make_return_message(ourmodule.http_405,'Method Not Allowed!','Only GET or POST method is allowed!')



  


def activity_exists_in_json(name):
    with open('activities.json', 'r') as f:
        data = json.load(f)
        activities = data['activities']
    if name in activities:
        return True
   
def remove_activity_from_json_list(name):
    with open("activities.json", "r") as f:
        database = json.load(f)
        database["activities"].remove(name)
    with open("activities.json", "w") as f:
        json.dump(database, f)

def add_activity(name):
    with open("activities.json", "r") as f:
        database = json.load(f)
        database["activities"].append(name)
    with open("activities.json", "w") as f:
        json.dump(database, f)

def get_reservation_details(reservation_id):
    # get reservation details
    with open('reservations.json', 'r') as f:
        data = json.load(f)
    # find reservation
    for activity in data:
        for day in data[activity]:
            for reservation in data[activity][day]:
                if int(reservation['id']) == int(reservation_id):
                    reservation['day'] = int(day)-1
                    reservation['room'] = activity
                    #return reservation
                    return reservation
    return None



def make_return_message(status_code,title,message):
    return ourmodule.make_return_message(status_code,title,message)

# get response of status code
def get_status_code(message):
    response_lines = message.split('\n')
    status_line = response_lines[0]
    status_code = status_line.split(' ')[1]
    return int(status_code)

# get p text:html p tag
def get_p_text(message):
    return message.split('<p>')[1].split('</p>')[0]
# get title:html title tag
def get_title(message):
    return message.split('<title>')[1].split('</title>')[0]




# create a socket object
server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
# get local machine name
host = ''
port = 8088
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