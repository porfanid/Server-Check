# Server-Check
This is a program that checks whether a list of servers are responding to certain requests.

# How does the program work

We do not want to overload the server, so the requests are being performed every 5 minutes minimum. If no user uses this service, then the logs are not being updated, so that the server does not have to create unnecessary requests.