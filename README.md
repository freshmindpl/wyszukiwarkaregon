# Polish REGON Internet Database BIR1

PHP bindings for the BIR1 (Baza Internetowa REGON 1) API (https://wyszukiwarkaregon.stat.gov.pl/appBIR/index.aspx).

[API Documentation](https://goo.gl/zxBf2o)

[![Latest Stable Version](https://poser.pugx.org/freshmindpl/wyszukiwarkaregon/v/stable)](https://packagist.org/packages/freshmindpl/wyszukiwarkaregon)
[![Build Status](https://travis-ci.org/freshmindpl/wyszukiwarkaregon.svg?branch=master)](https://travis-ci.org/freshmindpl/wyszukiwarkaregon)
[![Code Climate](https://codeclimate.com/github/freshmindpl/wyszukiwarkaregon/badges/gpa.svg)](https://codeclimate.com/github/freshmindpl/wyszukiwarkaregon)
[![Test Coverage](https://codeclimate.com/github/freshmindpl/wyszukiwarkaregon/badges/coverage.svg)](https://codeclimate.com/github/freshmindpl/wyszukiwarkaregon/coverage)

## Installation

The API client can be installed via [Composer](https://github.com/composer/composer).

In your composer.json file:

```js
{
    "require": {
        "freshmindpl/wyszukiwarkaregon": "~3.0"
    }
}
```

Once the composer.json file is created you can run `composer install` for the initial package install and `composer update` to update to the latest version of the API client.

## Basic Usage

Remember to include the Composer autoloader in your application:

```php
<?php
require_once 'vendor/autoload.php';

// Application code...
?>
```

Configure your access credentials when creating a client:

```php
<?php
use WyszukiwarkaRegon\Client;

$client = new Client([
   'key' => 'aaaabbbbccccdddd' //Optional key,
   'session' => 'abcdefghijklmnopqrstuvwxyz' //Session id if already logged in
]);
?>
```

## License

MIT license. See the [LICENSE](LICENSE) file for more details.
