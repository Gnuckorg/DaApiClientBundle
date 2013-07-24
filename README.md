DaApiClientBundle
=================

DaApiClientBundle is a Symfony2's bundle allowing to discuss in a simple and secure way with an API.

Installation
------------

Installation is a quick 2 step process.

### Step 1: Add in composer

Add the bundle in the composer.json file:

``` js
	// composer.json

	"require": {
		// ...
        "da/api-client-bundle": "dev-master"
    },
```

And update your vendors:

``` bash
    composer update      # WIN
    composer.phar update # LINUX
```

### Step 2: Declare in the kernel

Declare the bundle in your kernel:

``` php
	// app/AppKernel.php

	$bundles = array(
        // ...
        new Da\ApiClientBundle\DaApiClientBundle(),
    );
```

Configuration of your API
-------------------------

``` yaml
	# app/config/config.yml

	da_api_client:
	    api:
	        my_api_name:
	            url:           'https://my-domain/api'
	            api_token:     3e90o0xrzy4gsw4k0440sw4k4g8oog0ckoo4okgogs0wowo4sg
	            cache_enabled: true
```

Call of your API
----------------

``` php
	try
	{
		$api = $container->get('da_api_client.api.my_api_name')
		$parameters = array('offset' => 0, 'limit' => 20);
		$friends = $api->get('/friends', $parameters);
	}
	catch (ApiCallException $e)
	{
		switch ($e->getStatus()->getCode())
		{
			// Handle specific http error code here.
			case '404':
				// ...
				break;
		}
	}
```

You can use all basic REST methods the same way:

``` php
	$friends = $api->get('/friends', array(...));
	$status = $api->post('/friends/add', array(...));
	$status = $api->put('/friends/update', array(...));
	$status = $api->delete('/friends/remove', array(...));
```

You can retrieve the status of the last http request in this way:

``` php
	$status = $api->getLastStatus();
```

Documentation
-------------

You can find a more detailled documentation [here](https://github.com/Gnuckorg/DaApiClientBundle/blob/master/Resources/doc/index.md).

What about the API server side?
-------------------------------

Take a look at the DaApiServerBundle!