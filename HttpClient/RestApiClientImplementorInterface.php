<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\HttpClient;

/**
 * RestApiClientImplementorInterface is the interface that an RestApiClientImplementor
 * should implement to be used as an implementor by the RestApiClientBridge.
 *
 * @author Thomas Prelot
 */
interface RestApiClientImplementorInterface extends RestApiClientInterface
{
    /**
     * Set the base URL of the API (from which all the path will be relative to).
     *
     * @param string $baseUrl The base URL.
     */
    function setBaseUrl($baseUrl);

    /**
     * Set the API token to authenticate your client in the API.
     *
     * @param string $apiToken The API token.
     */
    function setApiToken($apiToken);

    /**
     * Enable or disable the cache.
     *
     * @param string $enableCache Should enable the cache or not?
     */
    function enableCache($enableCache);
}