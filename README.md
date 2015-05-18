Polish REGON Internet Database
======================

[![Dependency Status](https://www.versioneye.com/user/projects/54d4b6023ca08495310002dd/badge.svg?style=flat)](https://www.versioneye.com/user/projects/54d4b6023ca08495310002dd)

PHP client to query GUS (Główny Urząd Statystyczny) for information about company data based on NIP, Regon or KRS number.


Installation
======================

Install the library by adding it to your composer.json or running::

    php composer.phar require freshmindpl/wyszukiwarkaregon:~2.0
    
Quick Example
======================

Get catpcha code for user to solve
----------------------

```php
<?php
require vendor/autoload.php

use WyszukiwarkaRegon\Client as RegonClient;

//Initiate client
$client = new RegonClient();

//Some methods may throw exceptions so it's safer to catch them
//and process them in Your application
try {

    //Get session key from API
    //This session key should be stored for later use - search query
    $session_id = $client->get()->zaloguj();
    
    //This method returns catpcha image base64 encoded
    $captcha_image = $client->get()->pobierzCaptcha($session_id);

} catch (\Exception $e) {
    echo "There was an error.\n";
}
```

You need to show the image to the user and ask him/her to solve it before You can query the database for data.

```php
//User entered captcha solution
$captcha_solution = '.....';

try {

    if(!$client->get()->sprawdzCaptcha($session_id, $captcha_solution) {
        echo "Error: the captcha solution is not valid";
    }
} catch (\Exception $e) {
    echo "There was an error.\n";
}
```

After successful captcha validation You can start querying database for data.

Querying for data (searching)
----------------------

```php
//Search by NIP number
$params = [
    'Nip' => 1234567890,
    'Regon' => null,
    'Krs' => null
];

try {

    $data = $client->get()->daneSzukaj($session_id, $params);
} catch (\Exception $e) {
    echo "There was an error.\n";
}
```

License
======================

MIT license. See the LICENSE file for more details.
