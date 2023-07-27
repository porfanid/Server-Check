# Server-Check

[![License](https://img.shields.io/github/license/porfanid/Server-Check)](https://github.com/porfanid/Server-Check/blob/main/LICENSE)
[![SFTP Upload](https://github.com/porfanid/Server-Check/actions/workflows/deploy_to_server.yml/badge.svg)](https://github.com/porfanid/Server-Check/actions/workflows/deploy_to_server.yml)

## why was the program created
The program was created because many times I was trying to connect to a university server and that was not online. That was when I decided that I am going to create an app that checks if the server is actually online or not.

# Where is the app hosted?
The app is currently being hosted at the university of Ioannina servers under my university account. This is because this service was originally designed to be used by the university.

## How does the program work

This is a program that checks whether a list of servers are responding to certain requests by performing a simple connection to a given port. If that doesn't work, it moves forward to prforming a ping request. Based on that result, we can determine wether the server is online or offline.

We do not want to overload the server, so the requests are being performed every 5 minutes at maximum. If no user uses this service, then the logs are not being updated, so that the server does not have to create unnecessary requests and load the servers more than it has to.