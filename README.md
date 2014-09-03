# An easy way to intergrate Database Settings with Laravel

[![Latest Stable Version](https://poser.pugx.org/cornford/setter/version.png)](https://packagist.org/packages/cornford/setter)
[![Total Downloads](https://poser.pugx.org/cornford/setter/d/total.png)](https://packagist.org/packages/cornford/setter)
[![Build Status](https://travis-ci.org/bradcornford/Setter.svg?branch=master)](https://travis-ci.org/bradcornford/Setter)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bradcornford/Setter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bradcornford/Setter/?branch=master)

Think of Setter as an easy way to integrate Settings with Laravel 4, providing a variety of helpers to speed up the utilisation of application wide settings. These include:

- `Setting::set`
- `Setting::get`
- `Setting::forget`
- `Setting::has`
- `Setting::all`
- `Setting::clear`

## Installation

Begin by installing this package through Composer. Edit your project's `composer.json` file to require `cornford/setter`.

	"require": {
		"cornford/setter": "1.*"
	}

Next, update Composer from the Terminal:

	composer update

We now have to migrate the package database table with the following command:

    php artisan migrate --package cornford/setter

Once this operation completes, the next step is to add the service provider. Open `app/config/app.php`, and add a new item to the providers array.

	'Cornford\Setter\SettingServiceProvider',

The final step is to introduce the facade. Open `app/config/app.php`, and add a new item to the aliases array.

	'Setting'         => 'Cornford\Setter\Facades\Setting',

That's it! You're all set to go.

## Usage

It's really as simple as using the Setter class in any Controller / Model / File you see fit with:

`Setting::`

This will give you access to

- [Set](#set)
- [Get](#get)
- [Forget](#forget)
- [Has](#has)
- [All](#all)
- [Clear](#clear)

### Set

The `set` method sets a setting via both a key and a value parameter in the database.

	Setting::set('app.url', 'http://localhost');

### Get

The `get` method gets a setting via a key parameter from the database, and a default value can be optionally passed if the setting doesn't exist.
If no default parameter is supplied, and an application configration variable is present, this will be returned.

	Setting::get('app.url', 'http://localhost');
	Setting::get('app.url');

### Forget

The `forget` method removes a setting via a key paramter from the database.

	Setting::forget('app.setting');

### Has

The `has` method returns a true / false based on if a setting is present in the database via a key paramter.
This doesn't fall back to checking application configuration variables.

	Setting::has('app.setting');

### All

The `all` method returns an array of key value pairs of settings from the database.
This doesn't fall back to return application configuration variables.

	Setting::all();

### Clear

The `clear` removes all settings from the database.
This doesn't fall back to removing application configuration variables.

	Setting::clear();

### Expires

The `expires` sets the cache expiry setting.
Can be false to not cache, true / 0 to cache indefinitely, an integer for minutes, or a datetime of when to expire.

	Setting::expires(false);

### License

Setter is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)