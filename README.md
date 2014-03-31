DaApiClientBundle
=================

DaApiClientBundle is a Symfony2's bundle allowing to discuss in a simple and secure way with an API.


Installation
------------

### Step 1: Add in composer

Add the bundle in the composer.json file:

```json
// composer.json

"require": {
    // ...
    "doctrine/doctrine-cache-bundle": "~1.0",
    "da/auth-common-bundle": "dev-master",
    "da/api-client-bundle": "dev-master"
},
```

Update your vendors with composer:

```sh
composer update      # WIN
composer.phar update # LINUX
```

### Step 2: Declare in the kernel

Declare the bundle in your kernel:

```php
// app/AppKernel.php

$bundles = array(
    // ...
    new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
    new Da\ApiClientBundle\DaApiClientBundle(),
);
```

### Step 3: Setup the cache provider (optional)

To setup the cache, first define a provider using doctrine_cache.
And reference it in the da_api_client http_cacher parameters:

```yml
doctrine_cache:
    providers:
        da_api_client:
            memcache:
                servers:
                    memcached01:
                        host: localhost
                        port: 11211

da_api_client:
    http_cacher: doctrine_cache.providers.da_api_client
```

### Step 4: Setup the developpement environement (optional)

In order to profile api logs enabled it your `app/config/config_dev.yml`

```yml
...
da_api_client:
    log_enabled: true
```


Configuration to use your API
-----------------------------

```yaml
# app/config/config.yml

da_api_client:
    api:
        my_api_name:
            endpoint_root:  https://my-domain/api
            security_token: 3e90o0xrzy4gsw4k0440sw4k4g8oog0ckoo4okgogs0wowo4sg
            cache_enabled:  true
```


How to use
----------

```php
try {
    $api = $container->get('da_api_client.api.my_api_name')
    $parameters = array('offset' => 0, 'limit' => 20);
    $friends = $api->get('/friends', $parameters);
} catch (\Da\AuthCommonBundle\Exception\ApiHttpResponseException $e) {
    switch ($e->getStatusCode()) {
        // Handle specific http error code here.
        case '404':
            // ...
            break;
    }
}
```

You can use all basic REST methods in the same way:

```php
$friends = $api->get('/friends', array(...));
$friend  = $api->post('/friends/add', array(...));
$friend  = $api->put('/friends/update', array(...));
$status  = $api->delete('/friends/remove', array(...));
```


Documentation
-------------

You can do a lot more than that! Check the full [documentation](https://github.com/Gnuckorg/DaApiClientBundle/blob/master/Resources/doc/index.md)!


What about the API server side?
-------------------------------

Take a look at the [DaApiServerBundle](https://github.com/Gnuckorg/DaApiServerBundle)!

