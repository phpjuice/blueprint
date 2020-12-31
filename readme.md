# Blueprint

[![Latest Stable Version](https://poser.pugx.org/phpjuice/blueprint/v/stable)](https://packagist.org/packages/phpjuice/blueprint)
[![Total Downloads](https://poser.pugx.org/phpjuice/blueprint/downloads)](https://packagist.org/packages/phpjuice/blueprint)
[![License](https://poser.pugx.org/phpjuice/blueprint/license)](https://packagist.org/packages/phpjuice/blueprint)

Blueprint is a powerful CRUD generator to speed up the development of your laravel apps.

## Installation

Blueprint Package requires Laravel 5.5 or higher.

> **INFO:** If you are using an older version of Laravel this package may not function correctly.

The supported way of installing Blueprint package is via Composer.

```bash
composer require phpjuice/blueprint --dev
```

This package supports Package Auto Discovery feature on Laravel
5.5 or higher the service provider will automatically get registered. In older versions of the framework just add the service provider in `config/app.php` file:

```php
'providers' => [
    // ...
    PHPJuice\Blueprint\BlueprintServiceProvider::class,
];
```

After that, you can publish its [Template Files](https://github.com/PHPJuice/Blueprint/tree/master/src/Stubs) using the vendor:publish Artisan command:

```bash
php artisan vendor:publish --tag=blueprint.templates
```

You can publish the [Configuration](https://github.com/PHPJuice/Blueprint/blob/master/config/blueprint.php) file with:

```bash
php artisan vendor:publish --tag=blueprint.config
```

When published, the [`config/blueprint.php`](https://github.com/PHPJuice/Blueprint/blob/master/config/blueprint.php) config file contains:

```php
<?php

return [
    'custom_template' => false,
    /*
    |--------------------------------------------------------------------------
    | Blueprint Template Stubs Storage Path
    |--------------------------------------------------------------------------
    |
    | Here you can specify your custom template path for the generator.
    |
     */
    'path' => base_path('resources/vendor/blueprint/'),
];
```

## Usage

Blueprint crud generator is designed to be very simple and straightforward to use. All you need to do is to create a crud blueprint file then generate it.

### Creating a CRUD

Inorder to create a blueprint file we use the following artisan command :

```bash
php artisan blueprint:make CRUD_NAME
```

**Example:**

```bash
php artisan blueprint:make Post
```

After running this command a crud blueprint file will be generated under your database folder `database/blueprints`, the naming convention will follow laravel migrations naming conventions. ex: `2018_12_09_144004_create_post_crud_blueprint.json`

And here is a basic strucure for a blueprint json file,following laravel's naming conventions the crud will generate resource names from the `CRUD_NAME` arguement passed to `php artisan blueprint:make` command.

**Example:**

```json
{
    "crud": {
        "name": "Post",
        "namespace": "Posts",
        "isApi": true
    },
    "controller": {
        "name": "PostsController",
        "pagination": 10,
        "validations": []
    },
    "model": {
        "name": "Post",
        "fillable": "",
        "hidden": "",
        "softDeletes": false,
        "relationships": []
    },
    "table": {
        "name": "posts",
        "schema": {
            "fields": [],
            "keys": {
                "primary": "id",
                "foreign": [],
                "indexes": []
            },
            "softDeletes": false
        }
    },
    "route": {
        "name": "posts",
        "url": "posts",
        "middlewares": []
    }
}
```

### Generating a CRUD

Inorder to generate the crud we created we use the following command :

```bash
php artisan blueprint:generate CRUD_NAME
```

**Example:**

```bash
php artisan blueprint:generate Post
```

> **Note:** note that we are using the crud name not the curd file name, this command will try to look for a crud blueprint under your `database/blueprints` folder with the provided name, if none is found it will ask you to create a new crud under that name.

After running this command a the following files will be generated :

- Controller
- Model
- Request
- Response
- Migration
- Test

And by default, the generator will attempt to append the crud route to your Route file. following this snippet
`Route::apiResource('route-name', 'controller-name');`

### Runing Crud Migrations

After generating the curd and creating all resources, run migrate command:

```bash
php artisan migrate
```

## CRUD Blueprint Example

```json
{
    "crud": {
        "name": "Post",
        "namespace": "Content",
        "isApi": true
    },
    "controller": {
        "name": "PostsController",
        "namespace": "Content",
        "pagination": 10,
        "validations": [
            {
                "field": "title",
                "rules": "required|min:5|unique:posts"
            },
            {
                "field": "content",
                "rules": "required|min:5"
            }
        ]
    },
    "model": {
        "name": "Post",
        "namespace": "Content",
        "fillable": "title,content",
        "hidden": "user_id",
        "softDeletes": true,
        "relationships": [
            {
                "name": "user",
                "type": "belongsTo",
                "class": "App\\User"
            }
        ]
    },
    "table": {
        "name": "posts",
        "schema": {
            "fields": [
                {
                    "name": "title",
                    "type": "string"
                },
                {
                    "name": "content",
                    "type": "text"
                }
            ],
            "keys": {
                "primary": "id",
                "foreign": [
                    {
                        "column": "user_id",
                        "references": "id",
                        "on": "users",
                        "onDelete": "cascade",
                        "onUpdate": "cascade"
                    }
                ],
                "indexes": [
                    {
                        "field": "title",
                        "type": "unique"
                    },
                    {
                        "field": "title",
                        "type": "index"
                    }
                ]
            },
            "softDeletes": true
        }
    },
    "route": {
        "name": "posts",
        "namespace": "Posts",
        "url": "posts",
        "middlewares": []
    }
}
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](contributing.md) for details.

## Security

If you discover any security related issues, please email author instead of using the issue tracker.

## Credits

- [AppzCoder](https://github.com/appzcoder/crud-generator)

## License

license. Please see the [license file](LICENCE) for more information.

[![Latest Stable Version](https://poser.pugx.org/phpjuice/blueprint/v/stable)](https://packagist.org/packages/phpjuice/blueprint)
[![Total Downloads](https://poser.pugx.org/phpjuice/blueprint/downloads)](https://packagist.org/packages/phpjuice/blueprint)
[![License](https://poser.pugx.org/phpjuice/blueprint/license)](https://packagist.org/packages/phpjuice/blueprint)
