# awesome-admin
A admin framework of laravel

[中文版README](https://github.com/friparia/awesome-admin/blob/master/README.chs.md)

## Feature

 * Auto generated migrations
 * Auto generated admin control panel
 * Clearly written model actions 
 * One action can use in restful api and admin control panel
 * Role based permission control included
 * Absolutely compatible with Laravel and your previous codes or project

## Requirement

* Laravel 5

## Installation

First, add a line in the section of `require` in `composer.json` file:

    "friparia/admin": "dev-master"
    
and run `composer install`, or, you can execute the following command:
    
    composer require "friparia/admin:dev-master"

Then, add a line of service provider in `config/app.php`:
    
    Friparia\Admin\AdminServiceProvider::class,

In public directory run:
    
    bower install admin-lte
    
