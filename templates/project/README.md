## Init

In order to be able to generate sass and manage frontend librairies you have to install node, npm, sass and bower  

```bash
sudo apt-get install nodejs npm
sudo npm install -g sass bower
```

Librairies installation for dcumentations generation

```bash
sudo apt-get install grahviz plantuml imagemagick
sudo npm install jsdoc@3 -g
```

## Configuration

### Generate local ssl certificate

Set your domain name in Common name.

```bash
mkdir ssl
cd ssl
openssl genrsa -out server.key 2048
openssl req -new -key server.key -out server.csr
openssl x509 -req -days 365 -in server.csr -signkey server.key -out server.crt
```

Replace days by the number of days you want the certificate stay valid, there 1 years.  
Browsers will always trigger an ssl error but you won't have errors in logs anymore.

### VirtualHost

```bash
sudo vi /etc/apache2/sites-available/project.conf
```

```text
<VirtualHost *:443>
    ServerAlias project.com
    DocumentRoot /var/www/project/public
    Alias /docs /var/www/project/docs
    SSLEngine On
    SSLCertificateFile /var/www/project/ssl/server.crt
    SSLCertificateKeyFile /var/www/project/ssl/server.key

    <Directory /var/www/project/docs>
        AllowOverride All
        Options All
        Require all granted
    </Directory>

    <Directory /var/www/project/public>
        AllowOverride All
        Options All
        Require all granted
    </Directory>

    ErrorLog /var/log/apache2/project_error.log
    CustomLog /var/log/apache2/project_access.log combined
</VirtualHost>
```

```bash
sudo a2ensite project
```

Replace project by the name of your project.

### Documentation

#### PHP

Follow [PHPDoc](https://docs.phpdoc.org/3.0/guide/guides/index.html#guides) documentation.  
PHP generator is already configured to generate documentation from apps folder.

### JS

Follow [JSDoc 3](https://jsdoc.app/index.html) documentation.  
You need to update jsdoc/conf.json, project name and source to include folders in but don't forget to use an absolute path.

### CSS

Add to docs/css/css/doc.less all files to import containing css documentation as comments.  
Then compile public/*/css/include/css/main.scss to update the documentation, don't forget to clear browser cache.

### Generate

```bash
sudo sh ./generateDocs.sh
```
