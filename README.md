# Pusíci :heart:

## Installation

1. Install Composer from [getcomposer.org/download/](https://getcomposer.org/download/) by running commands

  ```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === 'e115a8dc7871f15d853148a7fbac7da27d6c0030b848d9b3dc09e2a0388afed865e6a3d6b3c0fad45c48e2b5fc1196ae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
  ```

2. Install php Slim framework

  ```
php composer.phar require slim/slim "^3.0"
  ```

3. Install php Illuminate database

  ```
php composer.phar require illuminate/database
  ```

4. Install php Respect validation

  ```
php composer.phar require respect/validation
  ```

5. Install php JSON Web Tokens (JWT) library

  ```
php composer.phar require firebase/php-jwt
  ```

6. Install Basic and JWT Authentication Middleware

  ```
php composer.phar require tuupola/slim-basic-auth
php composer.phar require tuupola/slim-jwt-auth
  ```

## Composer commands

```
php composer.phar
php composer.phar -h
php composer.phar install
php composer.phar update
// Dumps the autoloader - run each time the composer.json autoload section is changed
php composer.phar dump-autoload -o
// Lists all of the available packages
php composer.phar show
```

## XAMPP configuration
- set the DocumentRoot directory in Config -> Apache httpd.conf file
```
DocumentRoot "C:/Pusici-BE/www"
<Directory "C:/Pusici-BE/www">
```

## Links

### Documentation

- [Composer home](https://getcomposer.org/)
- [Composer Basic usage](https://getcomposer.org/doc/01-basic-usage.md)
- [Composer CLI](https://getcomposer.org/doc/03-cli.md)
- [Slim framework home](http://www.slimframework.com/)
- [Illuminate database docs](https://laravel.com/docs/5.3/database)
- [Illuminate database API](https://laravel.com/api/5.3/Illuminate/Database.html)
- [ORM in Laravel tutorial](https://scotch.io/tutorials/a-guide-to-using-eloquent-orm-in-laravel)
- [Respect Validation tutorial](https://www.sitepoint.com/validating-your-data-with-respect-validation/)
- [JWT tutorial](https://www.sitepoint.com/php-authorization-jwt-json-web-tokens/)
- [JWT Authentication Middleware](https://www.appelsiini.net/projects/slim-jwt-auth)
- [HTTP status codes](https://en.wikipedia.org/wiki/List_of_HTTP_status_codes)

### GitHub

- [Respect Validation](https://github.com/Respect/Validation)
- [PSR-7 Basic Auth Middleware](https://github.com/tuupola/slim-basic-auth)
- [PSR-7 JWT Authentication Middleware](https://github.com/tuupola/slim-jwt-auth)
