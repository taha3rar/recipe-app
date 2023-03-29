# Recipe App

This is a web application that allows users to search for recipes based on ingredients and create their own recipes. It is built using PHP, MySQL, and Bootstrap.

## Requirements

- PHP 7.0 or later
- MySQL 5.6 or later
- Apache or Nginx web server

## Installation

### Linux

1. Update the package manager: `sudo apt-get update`
2. Install Apache: `sudo apt-get install apache2`
3. Install PHP and required extensions: `sudo apt-get install php libapache2-mod-php php-mysql php-curl php-json php-gd php-mbstring php-xml php-zip`
4. Install MySQL: `sudo apt-get install mysql-server`
5. Create a new MySQL user and database:
    ```bash
    sudo mysql -u root -p
    CREATE DATABASE recipe_app;
    CREATE USER 'recipe_app'@'localhost' IDENTIFIED BY 'password';
    GRANT ALL PRIVILEGES ON recipe_app.* TO 'recipe_app'@'localhost';
    FLUSH PRIVILEGES;
    exit
    ```
6. Clone the repository: `git clone https://github.com/username/recipe-app.git`
7. Move the repository to the web server's document root: `sudo mv recipe-app /var/www/html`
8. Create a new virtual host configuration file: `sudo nano /etc/apache2/sites-available/recipe-app.conf`
9. Add the following configuration to the file and save it:
    ```
    <VirtualHost *:80>
        ServerName recipe-app.local
        ServerAlias www.recipe-app.local
        DocumentRoot /var/www/html/recipe-app/public
        <Directory /var/www/html/recipe-app/public>
            AllowOverride All
            Require all granted
        </Directory>
    </VirtualHost>
    ```
10. Enable the new virtual host: `sudo a2ensite recipe-app`
11. Restart Apache: `sudo service apache2 restart`
12. Open the application in a web browser: `www.recipe-app.local`

### Mac
1. Install [Homebrew](https://brew.sh/): `/usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"`
2. Install PHP: `brew install php`
3. Install MySQL: `brew install mysql`
4. Start MySQL: `mysql.server start`
5. Create a new MySQL user and database:
    ```bash
    mysql -u root -p
    CREATE DATABASE recipe_app;
    CREATE USER 'recipe_app'@'localhost' IDENTIFIED BY 'password';
    GRANT ALL PRIVILEGES ON recipe_app.* TO 'recipe_app'@'localhost';
    FLUSH PRIVILEGES;
    exit
    ```
6. Clone the repository: `git clone https://github.com/username/recipe-app.git`
7. Move the repository to the web server's document root: `sudo mv recipe-app /Library/WebServer/Documents`
8. Create a new virtual host configuration file: `sudo nano /etc/apache2/extra/httpd-vhosts.conf`
9. Add the following configuration to the file and save it:
    ```
    <VirtualHost *:80>
        ServerName recipe-app.local
        ServerAlias www.recipe-app.local
        DocumentRoot /Library/WebServer/Documents/recipe-app/public
        <Directory /Library/WebServer/Documents/recipe-app/public>
            AllowOverride All
            Require all granted
        </Directory>
    </VirtualHost>
    ```
10. Open the hosts file: `sudo nano /etc/hosts`
11. Add the following line to the file and save it: `127.0.0.1   recipe-app.local www.recipe-app.local`
12. Restart Apache: `sudo apachectl restart`
13. Open the application in a web browser: `www.recipe-app.local`

### Windows
1. Install [XAMPP](https://www.apachefriends.org/index.html)
2. Clone the repository: `git clone https://github.com/username/recipe-app.git`
3. Move the repository to the web server's document root: `move recipe-app C:\xampp\htdocs`
4. Open the XAMPP Control Panel and start the Apache and MySQL modules
5. Open a web browser and navigate to `localhost/phpmyadmin`
6. Create a new MySQL user and database:
    - Click on the "Users" tab
    - Click on the "Add user" link
    - Enter "recipe_app" for the username and "password" for the password
    - Click on the "Go" button
    - Click on the "Databases" tab
    - Click on the "Add database" link
    - Enter "recipe_app" for the database name
    - Click on the "Go" button
    - Click on the "Privileges" tab
    - Click on the "Add privileges" link
    - Select "recipe_app" for the database and "recipe_app" for the user
    - Click on the "Go" button
7. Open the application in a web browser: `localhost/recipe-app/public`
8. Open the hosts file: `C:\Windows\System32\drivers\etc\hosts`
9. Add the following line to the file and save it: `127.0.0.1   recipe-app.local www.recipe-app.local`
10. Open the XAMPP Control Panel and click on the "Config" button
11. Click on the "Apache (httpd.conf)" link
12. Search for the line that says `DocumentRoot "C:/xampp/htdocs"`
13. Change the path to `DocumentRoot "C:/xampp/htdocs/recipe-app/public"`
14. Click on the "OK" button
15. Restart Apache: `C:\xampp\apache_stop.exe` and `C:\xampp\apache_start.exe`
16. Open the application in a web browser: `www.recipe-app.local`




