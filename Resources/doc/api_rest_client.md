DaApiClientBundle REST API Client
=================================

You can define your own REST API client to help your application(s) to communicate with your API.
It allows you to create a specific bundle providing a reusable interface for all your applications.


How to use
----------
```php
try {
    $api = $container->get('da_api_client.api.my_rest_api_name')
    $parameters = array('offset' => 0, 'limit' => 20);
    $friends = $api->get('/friends', $parameters);
} catch (\Da\ApiClientBundle\Exception\ApiHttpResponseException $e) {
    switch ($e->getHttpCode()) {
        // Handle specific http error code here.
        case '404':
            // ...
            break;
        case '500':
            // ...
            break;
    }
}
```

Note: When you make a call to your API, if an error status code is returned (4xx or 5xx), an exception is thrown.

You can use all basic REST methods in the same way:
```php
$friends = $api->get('/friends', array(...));
$friend  = $api->post('/friends/add', array(...));
$friend  = $api->put('/friends/update', array(...));
$status  = $api->delete('/friends/remove', array(...));
$status  = $api->link('/friends/{id}', array(...));
$status  = $api->unlink('/friends/{id}', array(...));
```


Create your REST API client
----------------------------

First, you have to define a new class like this one:
``` php
<?php
// src/My/OwnBundle/Http/Rest/MyRestApiClient.php

namespace My\OwnBundle\HttpClient;

use Da\ApiClientBundle\Http\Rest\RestApiClientBridge;

class MyRestApiClient extends RestApiClientBridge
{
    // TODO: implements.
}
```

Then, you must define it as a service:
``` yaml
# src/My/OwnBundle/Resources/config/services.yml

service:
    my_own.my_rest_api_client:
        class:     My\OwnBundle\Http\Rest\MyRestApiClient
        arguments: [null, null] # You can add more arguments but let the first two null.
```


Link your API client to a configured API
----------------------------------------

``` yaml
# app/config/config.yml

da_api_client:
    api:
        my_api_name:
            endpoint_root:  'https://my-domain/api'
            security_token: 3e90o0xrzy4gsw4k0440sw4k4g8oog0ckoo4okgogs0wowo4sg
            client:    
                service: my_own.my_rest_api_client
```


Redefine the implementation of the API client
---------------------------------------------

The parent service `da_api_client.api` is part of a bridge pattern.
If you want to change the standard implementation for the API client, it is pretty easy.
First, you have to define your own implementor class:
``` php
<?php
// src/My/OwnBundle/Http/Rest/MyRestApiClientImplementor.php

namespace My\OwnBundle\Http\Rest;

use Da\ApiClientBundle\Http\Rest\AbstractRestApiClientImplementor;

class MyApiRestClientImplementor extends AbstractRestApiClientImplementor
{
    // TODO: implements.
}
```

Finally, you must precise it in the configuration:
``` yaml
# app/config/config.yml

da_api_client:
    api:
        my_api_name:
            endpoint_root:  'https://my-domain/api'
            security_token: 3e90o0xrzy4gsw4k0440sw4k4g8oog0ckoo4okgogs0wowo4sg
            client:    
                service: my_own.my_rest_api_client
                implementor: my_own.my_api_rest_client_implementor
```


Use your API client
-------------------

You are now able to use your API client.
For instance, you could have a method like this:
``` php
<?php
// src/My/OwnBundle/Http/Rest/MyApiClient.php

namespace My\OwnBundle\Http\Rest;

use Da\ApiClientBundle\Http\Rest\RestApiClientBridge;

class MyRestApiClient extends RestApiClientBridge
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
$friends = $container->get('da_api_client.api.my_rest_api_name')->getFriends(0, 20);
```
