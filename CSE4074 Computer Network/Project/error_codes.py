
import datetime


http_200='200 OK'
http_400='400 Bad Request'
http_403='403 Forbidden'
http_404='404 Not Found'
http_405='405 Method Not Allowed'



weekdays = ["MONDAY", "TUESDAY", "WEDNESDAY", "THURSDAY", "FRIDAY", "SATURDAY", "SUNDAY"]

def make_return_message(status_code,title,message):
    
    html_data = f"<html><head><title>{title}</title></head><body><p>{message}</p></body></html>"

    header_string = f"Date: {datetime.datetime.now().strftime('%a, %d %b %Y %H:%M:%S')}\n"
    header_string += f"Server: Python HTTP Server\n"
    header_string += f"Content-Length: {len(html_data)}\n"
    header_string += f"Connection: close\n"
    header_string += f"Content-Type: text/html\n"
    header_string += f"\n"

    
    return f"HTTP/1.1 {status_code}\n{header_string}{html_data.encode().decode('utf-8')}"