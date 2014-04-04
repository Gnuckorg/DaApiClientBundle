DaApiClientBundle
=================

DaApiClientBundle is a Symfony2's bundle allowing to discuss in a simple and secure way with an API.


Installation
------------

Add dependencies in your `composer.json` file:
```json
"require": {
    ...
    "doctrine/doctrine-cache-bundle": "~1.0",
    "da/api-client-bundle": "dev-master"
},
```

Install these new dependencies of your application:
```sh
$ php composer.phar update
```

Enable bundles in your application kernel:
```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
        new Da\ApiClientBundle\DaApiClientBundle(),
    );
}
```


Documentation
-------------

[Read the Documentation](Resources/doc/index.md)


Tests
-----

Install bundle dependencies:
```sh
$ php composer.phar update
```

To execute unit tests:
```sh
$ phpunit --coverage-text
```
