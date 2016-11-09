# Status API
================

This document describes how to install the API

## Installation

    $ composer install

## Configuration
```yaml
parameters:
    database_driver: pdo_sqlite                     #We use sqllite
    database_host:                                  #empty for sqllite
    database_port:                                  #empty for sqllite
    database_name: %kernel.root_dir%/db.sqlite      #path to sqllite file
    database_path: %kernel.root_dir%/db.sqlite      #path to sqllite file
    database_user:                                  #empty for sqllite
    database_password:                              #empty for sqllite

    #Mail settings
    mailer_transport:  smtp                             
    mailer_host:       127.0.0.1
    mailer_user:       ~
    mailer_password:   ~

    # A secret key that's used to generate certain security-related tokens
    secret:            ThisTokenIsNotSoSecretChangeIt

    email_from: info@intraway.com                   #Email address sender
    email_from_name: Intraway                       #Email name sender
    
    message_size: 120                               #Max message status length
    rows_per_page: 20                               #Default rows per page
    
    base_url: http://localhost/statusapi/api        #Base API URL 
```


## Database schema creation
This project uses SQLlite. You only has to access to the schema controller
    http://localhost/statusapi/setup

## API documentation
Follow the [RAML](docs/Status_API.raml) file for more information

