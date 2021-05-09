# Backpack\Settings

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Style CI](https://styleci.io/repos/53683729/shield)](https://styleci.io/repos/53683729)
[![Total Downloads][ico-downloads]][link-downloads]

An interface for the administrator to easily change application settings. Uses Laravel Backpack. Works on Laravel 5.2 to Laravel 8.

> ### Security updates and breaking changes
> Please **[subscribe to the Backpack Newsletter](http://backpackforlaravel.com/newsletter)** so you can find out about any security updates, breaking changes or major features. We send an email every 1-2 months.

## Install

In your terminal:

``` bash
# install the package
composer require backpack/settings

# run the migration
php artisan vendor:publish --provider="Backpack\Settings\SettingsServiceProvider"
php artisan migrate

# [optional] add a menu item for it to the sidebar_content file
php artisan backpack:add-sidebar-content "<li class='nav-item'><a class='nav-link' href='{{ backpack_url('setting') }}'><i class='nav-icon la la-cog'></i> <span>Settings</span></a></li>"

# [optional] insert some example dummy data to the database
php artisan db:seed --class="Backpack\Settings\database\seeds\SettingsTableSeeder"
```

### Config File - [optional] You may change according to your needs.
```
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    |
    | Database Settings Table Name
    |
    */
    'table_name' => 'settings',

    /*
    |--------------------------------------------------------------------------
    | Route Name
    |--------------------------------------------------------------------------
    |
    | Routing Name
    |
    */
    'route_name' => 'setting',
];

```

## Usage

### End user
Add it to the menu or access it by its route: **application/admin/setting**

### Programmer
Use it like you would any config value in a virtual settings.php file. Except the values are stored in the database and fetched on boot, instead of being stored in a file.

``` php
Setting::get('contact_email')
// or 
Config::get('settings.contact_email')
```

### Add new settings

Settings are stored in the database in the "settings" table. Its columns are:
- id (ex: 1)
- key (ex: contact_email)
- name (ex: Contact form email address)
- description (ex: The email address that all emails go to.)
- value (ex: admin@laravelbackpack.com)
- field (Backpack CRUD field configuration in JSON format. https://backpackforlaravel.com/docs/crud-fields#default-field-types)
- active (1 or 0)
- created_at
- updated_at

There is no interface available to add new settings. They are added by the developer directly in the database, since the Backpack CRUD field configuration is a bit complicated. See the field types and their configuration code on https://backpackforlaravel.com/docs

### Override existing configurations

You can use this addon to make various Laravel configurations adjustable through the settings GUI, including Backpack settings themself.
For example, you can override the Backpack `show_powered_by` or the `skin` setting in `/config/Backpack/base.php`.

1. Create the setting entry in your settings database. You can add the settings manually, or via [Laravel seeders](https://laravel.com/docs/seeding). The values inserted into the database should be look similar to below:

   For Backpack `show_powered_by` setting:

   | Field | Value |
   | --- | --- |
   | key | show_powered_by |
   | name | Showed Powered By |
   | description | Whether to show the powered by Backpack on the bottom right corner or not. |
   | value | 1 |
   | field | {"name":"value","label":"Value","type":"checkbox"} |
   | active | 1 |

   For Backpack `Skin` setting:

   | Field | Value |
   | --- | --- |
   | key | skin |
   | name | Skin |
   | description | Backpack admin panel skin settings. |
   | value | skin-purple |
   | field | {"name":"value","label":"Value","type":"select2_from_array","options":{"skin-black":"Black","skin-blue":"Blue",   "skin-purple":"Purple","skin-red":"Red","skin-yellow":"Yellow","skin-green":"Green","skin-blue-light":"Blue light",   "skin-black-light":"Black light","skin-purple-light":"Purple light","skin-green-light":"Green light","skin-red-light":"Red light",   "skin-yellow-light":"Yellow light"},"allows_null":false,"default":"skin-purple"} |
   | active | 1 |

2. Open up the `app/Providers/AppServiceProvider` file, and add the below lines:

   ```diff
   <?php
   
   namespace App\Providers;
   
   use Illuminate\Support\ServiceProvider;
   
   class AppServiceProvider extends ServiceProvider
   {
       /**
        * Bootstrap any application services.
        *
        * @return void
        */
       public function boot()
       {
   +       $this->overrideConfigValues();
       }
   
       /**
        * Register any application services.
        *
        * @return void
        */
       public function register()
       {
           //
       }
   
   +   protected function overrideConfigValues()
   +   {
   +       $config = [];
   +       if (config('settings.skin'))
   +           $config['backpack.base.skin'] = config('settings.skin');
   +       if (config('settings.show_powered_by'))
   +           $config['backpack.base.show_powered_by'] = config('settings.show_powered_by') == '1';
   +       config($config);
   +   }
   }
   ```

## Screenshots

See [backpackforlaravel.com](https://backpackforlaravel.com)

- List view:
![List / table view in Backpack/Settings](https://user-images.githubusercontent.com/1032474/111115626-8f7a0480-856d-11eb-99bb-3004ec621ebb.gif)
- Editing a setting with the email field type:

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Overwriting Functionality

If you need to modify how this works in a project: 
- create a ```routes/backpack/settings.php``` file; the package will see that, and load _your_ routes file, instead of the one in the package; 
- create controllers/models that extend the ones in the package, and use those in your new routes file;
- modify anything you'd like in the new controllers/models;

## Security

If you discover any security related issues, please email tabacitu@backpackforlaravel.com instead of using the issue tracker.

Please **[subscribe to the Backpack Newsletter](http://backpackforlaravel.com/newsletter)** so you can find out about any security updates, breaking changes or major features. We send an email every 1-2 months.

## Credits

- [Cristian Tabacitu][link-author]
- [All Contributors][link-contributors]

## License

Backpack is free for non-commercial use and 69 EUR/project for commercial use. Please see [License File](LICENSE.md) and [backpackforlaravel.com](https://backpackforlaravel.com/pricing) for more information.

## Hire us

We've spend more than 50.000 hours creating, polishing and maintaining administration panels on Laravel. We've developed e-Commerce, e-Learning, ERPs, social networks, payment gateways and much more. We've worked on admin panels _so much_, that we've created one of the most popular software in its niche - just from making public what was repetitive in our projects.

If you are looking for a developer/team to help you build an admin panel on Laravel, look no further. You'll have a difficult time finding someone with more experience & enthusiasm for this. This is _what we do_. [Contact us](https://backpackforlaravel.com/need-freelancer-or-development-team). Let's see if we can work together.


[ico-version]: https://img.shields.io/packagist/v/backpack/settings.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-dual-blue?style=flat-square
[ico-travis]: https://img.shields.io/travis/laravel-backpack/settings/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/laravel-backpack/settings.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/laravel-backpack/settings.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/backpack/settings.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/backpack/settings
[link-travis]: https://travis-ci.org/laravel-backpack/settings
[link-scrutinizer]: https://scrutinizer-ci.com/g/laravel-backpack/settings/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/laravel-backpack/settings
[link-downloads]: https://packagist.org/packages/backpack/settings
[link-author]: http://tabacitu.ro
[link-contributors]: ../../contributors
