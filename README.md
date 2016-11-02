# Pusici :heart:

## Installation
1. Install Composer from [getcomposer.org/download/](https://getcomposer.org/download/) by running commands

  ```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === 'e115a8dc7871f15d853148a7fbac7da27d6c0030b848d9b3dc09e2a0388afed865e6a3d6b3c0fad45c48e2b5fc1196ae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
  ```

2. Install php Slim framework by runing command

  ```
php composer.phar require slim/slim "^3.0"
  ```

## Links
- [Slim framework](http://www.slimframework.com/)
