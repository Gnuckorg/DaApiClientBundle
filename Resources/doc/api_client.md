DaApiClientBundle API Client
============================

You can define your own API client to help your application(s) to communicate with your API.
It allows you to create a specific bundle providing a reusable interface for all your applications.

Create your API client
----------------------

First, you have to define a new class like this one:

``` php
# src/My/OwnBundle/HttpClient/MyApiClient.php

namespace My\OwnBundle\HttpClient;

use Da\ApiClientBundle\HttpClient\RestApiClientBridge;

class MyApiClient extends RestApiClientBridge
{
    // TODO: implements.
}
```

Then, you must define it as a service:

``` yaml
# src/My/OwnBundle/Resources/config/services.yml

service:
    my_own.my_api_client:
        class: My\OwnBundle\HttpClient\MyApiClient
        parent: da_api_client.api
        abstract: false
        public: false
```

Link your API client to a configured API
----------------------------------------

``` yaml
# app/config/config.yml

da_api_client:
    api:
        my_api_name:
            base_url:       'https://my-domain/api'
            api_token: 3e90o0xrzy4gsw4k0440sw4k4g8oog0ckoo4okgogs0wowo4sg
            client:    
                service: my_own.my_api_client
```

Redefine the implementation of the API client
---------------------------------------------

The parent service `da_api_client.api` is part of a bridge pattern.
If you want to change the standard implementation for the API client, it is pretty easy.
First, you have to define your own implementor class:

``` php
# src/My/OwnBundle/HttpClient/MyApiClientImplementor.php

namespace My\OwnBundle\HttpClient;

use Da\ApiClientBundle\HttpClient\AbstractRestApiClientImplementor;

class MyApiClientImplementor extends AbstractRestApiClientImplementor
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
            base_url:       'https://my-domain/api'
            api_token: 3e90o0xrzy4gsw4k0440sw4k4g8oog0ckoo4okgogs0wowo4sg
            client:    
                service: my_own.my_api_client
                implementor: my_own.my_api_client_implementor
```

Use your API client
-------------------

You are now able to use your API client.
For instance, you could have a method like this:

``` php
# src/My/OwnBundle/HttpClient/MyApiClient.php

namespace My\OwnBundle\HttpClient;

use Da\ApiClientBundle\HttpClient\RestApiClientBridge;

class MyApiClient extends RestApiClientBridge
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
$friends = $container->get('da_api_client.api.my_api_name')->getFriends(0, 20);
```