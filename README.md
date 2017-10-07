IGAD (Internet Game Award Database)
=========

## Introduction


## Installation

Add `andach/igad` to your `composer.json`.
```
"andach/igad": "~1.0"
```
or 
```bash
composer require andach/igad
```

Run `composer update` to pull down the latest version of the package.

Now open up `app/config/app.php` and add the service provider to your `providers` array.

```php
'providers' => array(
    Andach\IGAD\IGADServiceProvider::class,
)
```

Optionally, add the facade to your `aliases` array
```php
'IGAD' => \Andach\IGAD\IGAD::class,
```

## Configuration

Add the `IGAD` to your `config/services.php` array
```php
'IGAD' => [
    'xboxapikey' => 'YOUR_IGAD_KEY'
]
```

## Usage

```php
// TODO

```

## Format of returned data

The returned JSON data is decoded as a PHP object.

## Run Unit Test

If you have PHPUnit installed in your environment, run:

```bash
$ phpunit
```


## Credits



## License

