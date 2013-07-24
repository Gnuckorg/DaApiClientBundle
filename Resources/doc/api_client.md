DaApiClientBundle API Client
============================

You can define your own API Client to help your application(s) to communicate with your API.
It allows you to create a specific bundle providing a reusable interface for all your applications.

Create your API Client
----------------------

First, you have to define a new class like this one:

``` php
	# src/My/OwnBundle/ApiClient/MyApiClient.php

	namespace My\OwnBundle\ApiClient;

	use Da\ApiClientBundle\ApiClient\BridgeApiClient;

	class MyApiClient extends BridgeApiClient
	{
	}
```

Then, you must define it as a service:

``` yaml
	# src/My/OwnBundle/Resources/config/services.yml

	service:
	    my_own.my_api_Client:
	        class: My\OwnBundle\ApiClient\MyApiClient
	        parent: da_api_client.api
	        abstract: false
	        public: false
```

Link your API Client to a configured API
----------------------------------------

``` yaml
	# app/config/config.yml

	da_api_client:
	    api:
	        my_api_name:
	            url:       'https://my-domain/api'
	            api_token: 3e90o0xrzy4gsw4k0440sw4k4g8oog0ckoo4okgogs0wowo4sg
	            Client:    
	                service: my_own.my_api_Client
```

Redefine the implementation of the API Client
---------------------------------------------

The parent service `da_api_client.api` is part of a bridge pattern.
If you want to change the standard implementation for the api Client, it is pretty easy.
First, you have to define your own implementor class:

``` php
	# src/My/OwnBundle/ApiClient/MyApiClientImplementor.php

	namespace My\OwnBundle\ApiClient;

	use Da\ApiClientBundle\ApiClient\ApiClientInterface;

	class MyApiClientImplementor implements ApiClientInterface
	{
	}
```

Finally, you must precise it in the configuration:

``` yaml
	# app/config/config.yml

	da_api_client:
	    api:
	        my_api_name:
	            url:       'https://my-domain/api'
	            api_token: 3e90o0xrzy4gsw4k0440sw4k4g8oog0ckoo4okgogs0wowo4sg
	            Client:    
	                service: my_own.my_api_Client
	                implementor: my_own.my_api_Client_implementor
```

Use your API Client
-------------------

You are now able to use your API Client.
For instance, you could have a method like this:

``` php
	# src/My/OwnBundle/ApiClient/MyApiClient.php

	namespace My\OwnBundle\ApiClient;

	use Da\ApiClientBundle\ApiClient\BridgeApiClient;

	class MyApiClient extends BridgeApiClient
	{
		public function getFriends($offset, $limit)
		{
			$parameters = array('offset' => $offset, 'limit' => $limit);
		
			return $this->get('/friends', $parameters);
		}
	}
```

Then, you can call it in this way:

``` php
	$api = $container->get('da_api_client.api.my_api_name')->getFriends(0, 20);
```