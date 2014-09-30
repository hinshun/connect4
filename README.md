# [Connect 4](http://github.com/hinshun/connect4)

Connect 4 is an web application written with PHP MVC framework CodeIgniter to play psuedo-real time Connect 4 and chat.

## Getting Started

First, we need setup our LEMP stack (Linux, Nginx, MySQL, PHP)

If you don't run a linux distribution, consider using
[Vagrant](https://www.vagrantup.com) to spin up a Ubuntu VM, or run one on
[Heroku](https://www.heroku.com) or
[DigitalOcean](https://www.digitalocean.com). They are fairly cheap.

Now let's get started. Always update first before `apt-get` installing anything.

    sudo apt-get update

### Step 1 - Installing our database

    sudo apt-get install mysql-server php5-mysql php5-gd

During the installation of MySQL, it will ask you to set your root password. Set
it to something you can remember, because we will have to change the
configurations in `application/config/database.php` later.

Once you have finished installing, run these to start and setup your MySQL
database

    sudo mysql_install_db
    sudo /usr/bin/mysql_secure_installation

The prompt will ask you for your password, type it in.

Next, it will ask you whether you want to change your password or not, say no,
and then yes to everything else.

### Step 2 - Installing Nginx

    echo "deb http://ppa.launchpad.net/nginx/stable/ubuntu $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/nginx-stable.list
    sudo apt-key adv --keyserver keyserver.ubuntu.com --recv-keys C300EE8C
    sudo apt-get update
    sudo apt-get install nginx

Nginx doesn't run automatically after installation.

    sudo service nginx start

### Step 3 - Installing PHP

    sudo apt-get install php5-fpm

That's it.

### Step 4 - Configuration

If you are running Ubuntu 12.04, stick configuring php-fpm to listen on
127.0.0.1:9000.

    sudo vim /etc/php5/fpm/php.ini
    # Add the following line to your php.ini
    extension=php_gd2.dll;

    sudo vim /etc/php5/fpm/pool.d/www.conf
    # Find the line listen = 127.0.0.1:9000 and make sure its not commented out
    # If you want to listen to a socket instead, change it to /var/run/php5-fpm.sock

    # Then restart php-fpm
    sudo service php5-fpm restart

Okay, hang in there. We're almost at the end of the tunnel. Let's configure
Nginx.

    vim /etc/nginx/sites-available/default

Change your configurations like such:

    [...]
    server {
      listen 80;

      root /home/vagrant/connect4;
      index index.html index.php;

      # Make site accessible from http://localhost/
      server_name localhost;

      location ~* \.(ico|css|js|gif|jpe?g|png)(\?[0-9]+)?$ {
        expires max;
        log_not_found off;
      }

      location / {
        try_files $uri $uri/ /index.php;
      }

      error_page 404 /404.html;

      # redirect server error pages to the static page /50x.html
      #
      error_page 500 502 503 504 /50x.html;
      location = /50x.html {
        root /home/vagrant/connect4;
      }

      # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
      #
      location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass 127.0.0.1:9000;
        # If you made the change earlier to use a socket, use the following line
        # instead
        # fastcgi_pass unix:/var/run/php5-fpm.sock;

        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
      }
    }
    [...]

Restart your Nginx for your changes to take affect

    sudo service nginx restart

### Step 5 - Creating your Database

Create your database first in MySQL

    mysql -u root -p
    # Type in the password you set in the beginning

    CREATE DATABASE connect4;

And create a user to access your database. We don't want to be doing this as
root

    create user 'username'@'localhost' identified by 'password';
    grant all privileges ON * . * to 'username'@'localhost';
    flush privileges;

Now connect CodeIgniter with the database you just set up.

    vim application/config/database.php
    # Change your username, password to match the user you just created

Once that's complete let's enable migration

    vim application/config/migration.php
    # Set migration_enabled to be TRUE

    # Visit the page http://localhost:80/index.php/migrate/index to start the
    migration

Check that your tables have been created

    mysql -u username -p connect4
    # Enter the password you set for your Connect 4 user

    show tables;

Don't forget to set your `migration_enabled` back to false.

And... you're done! Go nuts!

## Contributing

Anyone and everyone is welcome to contribute to Connect 4. Just fork the repo,
create your feature-branch or bug fix, and submit a pull request!
