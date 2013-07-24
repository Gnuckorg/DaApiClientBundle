DaApiClientBundle Configuration Reference
=========================================

All available configuration options are listed below with their default values.

``` yaml
	# app/config/config.yml

	da_api_client:
	    api:
	        my_api_name: # Your api name.
	            url:           ~    # [Required] The base url of your API.
	            api_token:     ~    # [Required] An API token to authenticate your client in your API.
	            cache_enabled: true # Use the cache feature if the response of your API says it can be set in cache.
	        ... # You can define multiple api.
```