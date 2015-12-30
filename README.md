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
use WyszukiwarkaRegon\Exception\RegonException;
use WyszukiwarkaRegon\Exception\SearchException;

$client = new Client([
   'key' => 'aaaabbbbccccdddd' //Optional api key - required for full reports,
   'session' => 'abcdefghijklmnopqrstuvwxyz' //Session id if already logged in
]);
?>
```

### Local Testing

Run `phpunit` from the project root to start all tests.

### Examples

#### Login

```php
<?php
// Login and obtain session id (sid)
try {
    $session_id = $client->login();
} catch (RegonException $e) {
    echo "There was an error.\n";
}

if(empty($session_id)) {
    // Empty session means that api key is invalid
    
    throw new \Exception('Invalid api key');
}

```

#### Logout

```php
<?php
// Login and obtain session id (sid)
try {
    $client->login();
} catch (RegonException $e) {
    echo "There was an error.\n";
}

....

// Invalidate current session
$client->logout();

```

#### Captcha

```php
<?php
// Login or set session_id if You have it

// This method returns captcha image base64 encoded
try {
    $captcha_image = $client->captcha();
} catch (RegonException $e) {
    echo "There was an error.\n";
}

```

```php
<?php
// User entered captcha solution
$code = '.....';

try {
    $response = $client->verify($code);
} catch (RegonException $e) {
    echo "There was an error.\n";
}

if(!$response) {
    echo "Wrong captcha code.\n";
}

```

#### Search

```php
<?php

$params = [
    'Regon' => 142396858, // 9 or 14 digits
    'Krs' => null, // 10 digits
    'Nip' => null, // 10 digits
    'Regony9zn' => null, // Multiple 9 digits Regon's seperated by any non digit char (max 100)
    'Regony14zn' => null, // Multiple 14 digits Regon's seperated by any non digit char (max 100)
    'Krsy' => null, // Multiple 10 digits Krs seperated by any non digit char (max 100)
    'Nipy' => null, // Multiple 10 digits Nip seperated by any non digit char (max 100)
];

try {
    $data = $client->search($params);
    
} catch (SearchException $e) {
    switch($e->getCode()) {
        case GetValue::SEARCH_ERROR_CAPTCHA: //Captcha resolve needed
            // Some code
            break;
        case GetValue::SEARCH_ERROR_INVALIDARGUMENT: //Wrong search params
            // Some code
            break;
        case GetValue::SEARCH_ERROR_NOTFOUND: //Empty result - no data found matching search params
            // Some code
            break;
        case GetValue::SEARCH_ERROR_SESSION: //Wrong session id or expired session
            // Some code
            break;
    }
} catch (RegonException $e) {
    echo "There was an error.\n";
}
```

#### Reports


### Full example

## License

MIT license. See the [LICENSE](LICENSE) file for more details.
