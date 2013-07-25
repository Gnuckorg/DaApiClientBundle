DaApiClientBundle Configuration Reference
=========================================

All available configuration options are listed below with their default values.

``` yaml
# app/config/config.yml

da_api_client:
    api:
        my_api_name: # Your api name.
            base_url:      ~    # [Required] The base url of your API (from which all path will be related to).
            api_token:     ~    # [Required] An API token to authenticate your client in your API.
            cache_enabled: true # Use the cache feature if the response of your API says it can be set in cache.
            client:
                service:     da_api_client.api             # The API client service. Define your own to provide an easy and sharable interface to the API.
                implementor: da_api_client.api_implementor # The API client implementor service. Define your own if you want to change the behaviour of the communication with the API.
        ... # You can define multiple api.
```