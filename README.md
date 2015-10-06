# phalconTool

This tool allows to manage project with Phalcon MVC Framework access on MYSQL.
#### Features :
- Create skeleton project application width default User controller using google connect
- User base database with social relation 
- Manage multi application/environment
- Generate models from Mysql database with column map and full relations
- Internal library Management
- Full Rest Api
- SCRUD on the fly with models relations and validations
- Generate controller/action
- Generate js/less template

#### in progress
- Find function Rest Api
- Scrud index
- Translation management
- Bdd migrations (first migration)
- Cli support inline
- Documentation

## Installation

#### Requirement
- [Phalcon](https://phalconphp.com/fr/download)
- php >=5.4

using composer
```
{
    "require": {
        "v-cult/phalcon": "dev-master"
    }
}
```

Create a phalcon symlink to application.php in your root project folder  
```
sudo ln -s /var/www/project/vendor/v-cult/phalcon/application.php /var/www/project/phalcon
```
or in /usr/bin to be used globally
```
sudo ln -s /var/www/project/vendor/v-cult/phalcon/application.php /usr/bin/phalcon
```

## Quick start

For all commands, you can specify the environment and application with options --env= and --app=   
The default values are dev/frontend

### Create project
```
phalcon generate:project
```
It will create apps and public dir in the root project folder initialized with frontend application.
The Document root of the server must be the public dir.
By default api et scrud libraries are enbaled and the project is secured by google user connect.
The default action user/login redirect to the google user authentification to log in the application and redirect to the SCRUD index (in progress).

### Generate models
Before generate models, don't forget to modify the config.php in your app folder.
```
phalcon generate:models
```
Models will be created from the database with column map and all relations.
You're now able to access to SRUD action for all model, example fro User :
```
http//localhost/scrud/User/read?id=1
http//localhost/scrud/User/create
http//localhost/scrud/User/search
```
You can merge all model which has one relation like User and UserSocial like this :
```
http//localhost/scrud/User UserSocial/read?id=1
http//localhost/scrud/User UserSocial/create
http//localhost/scrud/User UserSocial/search
```
So you can set as model as hasOne relations exists.

### Generate controllers and actions
You can specify one or more action associated with the controller, by default the views associated with the actions are created but the option --no_view=true block this.
```
phalcon generate:controller home index,test
```
It wiil create the home controller with the index et test actions with their views.
