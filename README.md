<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Installation

Get started with Makefile:

1. Run `make fileMode`
2. Certify your `.env` is configured correctly
2. Run `make install`
3. Make sure all containers is up and working
4. Make sure you have the databases created in your postgres database (production and test. See `.env`)
5. Run `make keys`
6. Run `make migrate`

# Project information

This project is a service for scrape [ISO 4217](https://pt.wikipedia.org/wiki/ISO_4217#C%C3%B3digos_ISO_para_moedas) data. Here PHP with Laravel and Python with Flask are used.


# Using the project

#### Create a user:

Endpoint(POST): http://10.10.0.22/api/signup

    {
        "email": "myemail@email.com",
        "password": "mypassword",
    }

#### After you must authenticate to get your bearer token:

Endpoint(POST): http://10.10.0.22/api/signin

    {
        "email": "myemail@email.com",
        "password": "mypassword",
    }

#### Now you are logged and can access others endpoints


- `To scrape data`:

1. Run `php artisan queue:work"` in your php container
2. Send a request (POST) with your bearer token to the URL http://10.10.0.22/api/scrape specifying the ISO 4217 param

#### Examples of valid requests:

- With `code` param:


    {  
        "code": "GBP"
    }


- With `code_list` param:


    {  
        "code_list": ["USD", "BRL]
    }


- With `number` param:


    {  
        "number": 840
    }


- With `number_list` param:


    {  
        "number_list": [840, 404] 
    }


## Running unit tests

- Run `make run-tests` to run all tests
- Run `make run-coverage` to run all tests and to generate coverage report
