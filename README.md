# Slovak Address API
 
[![Donate on PayPal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://paypal.me/stevo4) 
 
[Online DEMO](https://address.stefanbartko.sk/)

## Installation
 - run `composer create-project stefano/address my-project`
 - create database `database.sql`
 - rename .end.dist to .env and fill it with your settings
 - run import `bin/console import:address`

## API Endpoints Documentation

### GET /api/address
- fields [array] [optional]
    - [string] street, city, postcode, post_office
- filters [array] [optional]
    - street [string] [optional]
    - city [string] [optional]
    - postcode [string] [optional]
    - post_office [string] [optional]        
- orders [array] [optional]
    - [string] street, city, postcode, post_office
- order-direction [asc or desc] [optional] default asc 
- limit [number] [optional] default 20 
- offset [number] [optional] default 0
 
Response:
```
{
    items: [
        city: string,
        number, string,
        postcode: string,
        post_office: string
    ]
}
```  