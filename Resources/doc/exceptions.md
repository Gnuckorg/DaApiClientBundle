DaApiClientBundle Exceptions
============================

When you make a call to your API, if an error status code is returned (4xx or 5xx), an exception is thrown. 

``` php
try
{
    $api = $container->get('da_api_client.api.my_api_name')
    $parameters = array('offset' => 0, 'limit' => 20);
    $friends = $api->get('/friends', $parameters);
}
catch (\Da\AuthCommonBundle\Exception\ApiHttpResponseException $e)
{
    switch ($e->getStatusCode())
    {
        // Handle specific http error code here.
        case '404':
            // ...
            break;
    }
}
```

You can use all basic REST methods the same way: