# phalconTool

This is an alternative to the official [Phalcon Devtools](https://docs.phalcon.io/5.0/fr-fr/devtools).  
This tool allows you to manage projects with Phalcon MVC Framework accessing MYSQL.  
For Phalcon 4 and php7 use v4 branche, for JavaScript support use javascript branch.  

## Features

- Create skeleton project application with default User controller.
- Users and Langs/Translations management base.
- Manage multi application/environment.
- Generate models from Mysql database with column map and full relations.
- Internal library Management.
- Full Rest Api.
- Database migration engine and data management.
- SCRUD on the fly with models relations and validations.
- Generate controller/action.
- Generate js/sass/helper templates and builds.

## Documentation

[PHP Documentation](https://corentin-begne.github.io/phalconTool/)

# Installation

## Requirement

- [Phalcon = 5.x.x](https://phalconphp.com/fr/download)
- php = 8.x.x

Using Composer:

```json
{
    "require": {
        "v-cult/phalcon": "dev-master"
    }
}
```

Create a Phalcon symlink to application.php in your root project folder:

```bash
cd /var/www/project/
ln -s vendor/v-cult/phalcon/application.php ./phalcon
```

Or in /usr/bin to be used globally:

```bash
sudo ln -s /var/www/project/vendor/v-cult/phalcon/application.php /usr/bin/phalcon
```

## Quick start

For all commands, you can specify the environment and application with options `--env=` and `--app=`.  
The default values are dev/frontend. You can add any -- option; it will be available as a define in the task.

### Create project

```bash
./phalcon generate:project
```

This command will create the `apps` and `public` directories in the root project folder, initialized with the frontend application.  
The document root of the server must be the `public` directory, referring to the [readme.md](https://github.com/corentin-begne/phalconTool/blob/master/templates/project/README.md) file added in the project.  
By default, the `api` and `scrud` libraries are not enabled.  
You can also create any another app like `backend` for example.

```bash
./phalcon generate:app backend
```

If you don't need User management, remove `beforeDispatch` from security in `services.php`.

### Generate models

Before generating models, don't forget to modify the `config.php` file in your app folder.  
On generation, if the database is empty, it will import `defaultModels.sql` from the `templates` folder.

```bash
./phalcon models:generate
```

Models will be created from the database with column mapping and all relations.  
By default, SCRUD is not activated.  
You need to add `api` and `scrud` in the `config.php` file inside the `libraries` array, uncomment path in `SecurityPlugin` to add them in components and add them to the private or public resources.  
You are now able to access SCRUD actions for all models.  
For example, for User:

```text
https//localhost/frontend/scrud/User/read?id=1
https//localhost/frontend/scrud/User/create
https//localhost/frontend/scrud/User/search
```

You can merge all models that have a `hasOne` relation.  
For example, if you add the UserProfil table, you can access it like this:

```text
https//localhost/frontend/scrud/User UserProfil/read?id=1
https//localhost/frontend/scrud/User UserProfil/create
https//localhost/frontend/scrud/User UserProfil/search
```

This way, you can set up models with `hasOne` relations.  
If you don't need User manager remove tables `User` and `PermissionType`.  

You can access to models listing:  

```text
https//localhost/frontend/scrud/
```

### Generate controllers and actions

You can specify one or more actions associated with the controller.  
By default, the views associated with the actions are created, but the option `--no_view=true` blocks this.

```bash
./phalcon generate:controller home index,test
```

This command will create the `home` controller with the `index` and `test` actions, along with their views.

### Generate forms

You can generate forms that contain all inputs corresponding to their model field types, param must be a model name.

```bash
./phalcon generate:form PermissionType
```

### Generate tasks

Similar to generating controllers, you can create tasks with multiple actions at once.

```bash
./phalcon generate:task video convert,purge
```

Once created, these tasks can be accessed like any other task.

```bash
./phalcon video:purge
```

### Generate Sass and Js

The asset manager sets default collections for CSS and JS.  
There is a main CSS file that should be generated compiling Sass and two JS files (main and manager).  
The main file is the entry point for each collection.  
These collections are included in the default layout.  
You can generate these files for each controller/action, and for the index, it will be placed in the root folder.  
You can also generate global JS helpers.  
Use an empty action for index.

```bash
./phalcon generate:scss home ,test
./phalcon generate:js home ,test
./phalcon generate:js helper form
```

You can compile all Sass globally or for a specific controller/action, use empty action for index.  
If you specify the `prod` environment, the path will be modified by the first CDN configured, and the version will be updated from `config.php`.  
Js gnerated are `ES6` modules, you can use importmap, you need to create a `importmap.json` inside the `public` folder, file is used on dev environment and to resolve paths on build.

```bash
./phalcon generate:sass
./phalcon generate:sass home
./phalcon generate:sass home ,test
./phalcon generate:sass --env=prod
```

### Generate Js build

You can generate a JavaScript build file for each controller/action or globally, use emepty action for index.

```bash
./phalcon generate:build home ,test
./phalcon generate:build home
./phalcon generate:build
```

This command will generate a `build.mjs` file in the action directory, which will be automatically used in the production environment.

### Translation

Inside each view, you can use `$t->_`or `$t->__` to get translations from keys inside the array from the `messages` folder.  
The language used corresponds to the browser language, using `en` as the default.  
`$t->_` is more specific and allows access to the translations within the controller/action context.  
The translation functions are accessible from the controller, so inside the action, use `$this->__` and `$this->_` to access to the translations.

```php
// View inside controller user action login
$t->_('hi') // $messages['hi']
$t->__('hi') // $messages['user_login_hi']
```

You can use SCRUD to manage translations using the `LangMessage` default table or directly un files inside `messages` folder.  
Use the task to import (files to database) or export (database to files) data:

```bash
./phalcon message:import
./phalcon message:export
```

### Migration

The migration system is based on annotations and modifications made in the models.  
It reports the differences from the database.

```bash
./phalcon migration:generate
```

Once generated, you can run the migration.

```bash
./phalcon migration:run
```

Running this command will execute all the migrations from the current version (env_version inside `migrations` folder) to the last one.  
You can also roll back one migration at a time.

```bash
./phalcon migration:rollback
```

Or roll back until a specific version.

```bash
./phalcon migration:rollback 1
```

You can also create a model template.

```bash
./phalcon generate:model test
```

### Data Export

Run this command to export tables from the database.  
If you leave the parameter empty, all tables will be exported.  
Tables will be exported as CSV files in the `dumps` folder.

```bash
./phalcon data:export User
```

### Data Import

All CSV files in the `dumps` folder will be imported. Add the `truncate` option to truncate tables before importing.  
Otherwise, it will perform a replace based on the primary key.

```bash
./phalcon data:import --truncate
```

### CDN

You can configure CDN URLs in the `config.php` file.  
Typically, CDN URLs are only used for the production environment.  
If you don't need or want to remove this functionality in the services.php file, leave an empty folder.

### Make a release and send to env server

There's a task made for this purpose, you just need to specify the environment.  
You can configure it in the `config.php` file by setting the private key, args, excludes, login, and server.  
In the preprod and prod environment, it will compile Sass and build JS before sending the files using rsync.

```bash
./phalcon release --env=prod
```

### Libraries

You can add your own libraries by placing them inside the `libraries` folder.  
Libraries can include controllers, config files (loader, router), views (layouts, partials, views), and public data.  
Once added, simply include them in the `config.php` file within the `libraries` array.

### CLI

The CLI (Command Line Interface) has default services with MySQL, but you can define your own by creating a file named `services_cli.php` inside the `config` folder.
