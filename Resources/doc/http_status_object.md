DaApiClientBundle HTTP Status Object and Exceptions
===================================================

Exceptions
----------

When you make a call to your API, if an error status code is returned (4xx or 5xx), an exception is thrown. 

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

HTTP Status Object
------------------

You can retrieve the status of the last http request in this way:

``` php
	$status = $api->getLastStatus();
```

A class of status should implement the `Da\ApiClientBundle\Http\StatusInterface`.
You can then retrieve some useful informations:

``` php
	$code = $status->getCode(); // Get the code of the HTTP response.
```