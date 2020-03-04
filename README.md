# An easy way to intergrate Database Settings with Laravel

[![Latest Stable Version](https://poser.pugx.org/cornford/setter/version.png)](https://packagist.org/packages/cornford/setter)
[![Total Downloads](https://poser.pugx.org/cornford/setter/d/total.png)](https://packagist.org/packages/cornford/setter)
[![Build Status](https://travis-ci.org/bradcornford/Setter.svg?branch=master)](https://travis-ci.org/bradcornford/Setter)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bradcornford/Setter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bradcornford/Setter/?branch=master)

### For Laravel 5.x, check [version 2.4.0](https://github.com/bradcornford/Setter/tree/v1.5.5)

### For Laravel 4.x, check [version 1.7.2](https://github.com/bradcornford/Setter/tree/v1.5.5)

Think of Setter as an easy way to integrate Settings with Laravel, providing a variety of helpers to speed up the utilisation of application wide settings. These include:

- `Setting::set`
- `Setting::get`
- `Setting::forget`
- `Setting::has`
- `Setting::all`
- `Setting::clear`
- `Setting::expires`

## Installation

Begin by installing this package through Composer. Edit your project's `composer.json` file to require `cornford/setter`.

	"require": {
		"cornford/setter": "3.*"
	}

Next, update Composer from the Terminal:

	composer update

We now have to publish the packages assets with the following command:

	php artisan vendor:publish --provider="Cornford\Setter\Providers\SettingServiceProvider" --tag=setting

We now have to migrate the package database table with the following command:

	php artisan migrate

Once this operation completes, the next step is to add the service provider. Open `config/app.php`, and add a new item to the providers array.

	Cornford\Setter\Providers\SettingServiceProvider::class,

The final step is to introduce the facade. Open `config/app.php`, and add a new item to the aliases array.

	'Setting' => Cornford\Setter\Facades\SettingFacade::class,

That's it! You're all set to go.

## Configuration

You can now configure Setter in a few simple steps. Open `config/packages/cornford/setter/config.php` and update the options as needed.

- `cache` - Enable caching to improve performance by reducing database calls.
- `tag` - A tag prefixed to all cache items, e.g. tag::.
- `expiry` - The default expiry for cache items, e.g. 60.

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
- [expires](#expires)
- [Uncached](#uncached)
- [cacheEnabled](#cache-enabled)
- [enableCache](#enable-cache)
- [disableCache](#disable-cache)
- [setCacheTag](#set-cache-tag)
- [getCacheTag](#get-cache-tag)
- [cacheHas](#cache-has)
- [cacheForget](#cache-forget)
- [cacheClear](#cache-clear)
- [CacheExpires](#cache-expires)

### Set

The `set` method sets a setting via both a key and a value parameter in the database.

	Setting::set('app.url', 'http://localhost');

### Get

The `get` method gets a setting via a key parameter from the database, and a default value can be optionally passed if the setting doesn't exist.
If no default parameter is supplied, and an application configuration variable is present, this will be returned.

	Setting::get('app.url', 'http://localhost');
	Setting::get('app.url');

### Forget

The `forget` method removes a setting via a key parameter from the database.

	Setting::forget('app.setting');

### Has

The `has` method returns a true / false based on if a setting is present in the database via a key parameter.
This doesn't fall back to checking application configuration variables.

	Setting::has('app.setting');

### All

The `all` method returns an array of key value pairs of settings from the database.
This doesn't fall back to return application configuration variables.

	Setting::all();

### Clear

The `clear` method removes all settings from the database.
This doesn't fall back to removing application configuration variables.

	Setting::clear();

### Expires

The `expires` method sets the cache expiry setting.
Can be false to not cache, true / 0 to cache indefinitely, an integer for minutes, or a datetime of when to expire.

	Setting::expires(false);

### Uncached

The `uncached` method ensures the next get request is requested from the database rather than the cache. It will also re-cache the item if one is found.

	Setting::uncached();
	Setting::uncached()->get('app.setting');

### Cache Enabled

The `cacheEnabled` method gets the current caching state returning a true / false based on the cache status, retuning the current Setter instance.

	Setting::cacheEnabled();

### Enable Cache

The `enableCache` method sets caching state to cache items, retuning the current Setter instance.

	Setting::enableCache();
	Setting::enableCache()->set('app.url', 'http://localhost');

### Disable Cache

The `disableCache` method sets caching state to not cache items.

	Setting::disableCache();
	Setting::disableCache()->set('app.url', 'http://localhost');

### Set Cache Tag

The `setCacheTag` method sets the currently caching prefix tag.

	Setting::setCacheTag('tag:');

### Get Cache Tag

The `getCacheTag` method gets the currently set caching prefix tag.

	Setting::getCacheTag();

### Cache Has

The `cacheHas` method returns a true / false based on if a setting is present in the cache via a key parameter.
This doesn't fall back to checking application configuration variables.

	Setting::cacheHas('app.setting');

### Cache Forget

The `cacheForget` method removes a setting via a key parameter from the cache.

	Setting::cacheForget('app.setting');

### Cache Expires

The `cacheExpires` method sets the cache expiry setting.
Can be false to not cache, true / 0 to cache indefinitely, an integer for minutes, or a datetime of when to expire.

	Setting::cacheExpires(false);

### Cache Clear

The `cacheClear` method removes all settings from the cache.
This doesn't fall back to removing application configuration variables.

	Setting::cacheClear();

### License

Setter is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)