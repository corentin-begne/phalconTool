# Documentation

## php
in the root directory
```
php sami.phar update samiConfig.php
```
http://dev.sample.com/docs/php

## js
Need jsdoc
```
sudo npm install -g jsdoc@"<=3.3.0"
```
in the root directory
```
sudo rm -rf docs/js/*;
sudo jsdoc -p -r -t jsdoc/bower_components/jaguarjs-jsdoc -c jsdoc/conf.json -d docs/js --verbose
```
http://dev.sample.com/docs/js

## css
Need ruby and sass
```
sudo apt-get install ruby-full
sudo gem install sass --no-user-install
```
For any change you need to compile public/frontend/include/css/main.scss  
For any new documentation scss file, add it in docs/css/css/doc.less as "@import url("docs/file.less");" docs is a sym link pointing in functions css folder so each documentation need to be inside.

http://dev.sample.com/docs/css

# Vhost
```
sudo vi /etc/apache2/sites-available/sample.conf

<VirtualHost *:80>
    ServerName dev.sample.com
    Alias /docs /var/www/sample/docs
    DocumentRoot /var/www/sample/public

    <Directory /var/www/sample/docs>
        # enable the .htaccess rewrites
        AllowOverride All
        Options All
        Require all granted
    </Directory>

    <Directory /var/www/sample/public>
        # enable the .htaccess rewrites
        AllowOverride All
        Options All
        Require all granted
    </Directory>

    ErrorLog /var/log/apache2/sample_error.log
    CustomLog /var/log/apache2/sample_access.log combined
</VirtualHost>

sudo a2ensite sample
sudo vi /etc/hosts
localhost dev.sample.com
```