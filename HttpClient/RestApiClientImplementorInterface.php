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
     * Set the endpoint root URL of the API (from which all the path will be relative to).
     *
     * @param string $endpointRoot The api endpoint root URL.
     * @return RestApiClientImplementorInterface.
     */
    function setEndpointRoot($endpointRoot);

    /**
     * Get the endpoint root URL of the API (from which all the path will be relative to).
     *
     * @return string The api endpoint root URL.
     */
    function getEndpointRoot();

    /**
     * Set the security token to authenticate your client in the API.
     *
     * @param string $securityToken The security token.
     * @return RestApiClientImplementorInterface.
     */
    function setSecurityToken($securityToken);

    /**
     * Get the security token to authenticate your client in the API.
     *
     * @return string The security token.
     */
    function getSecurityToken();

    /**
     * Enable or disable the cache.
     *
     * @param bool $enableCache Should enable the cache or not
     * @return RestApiClientImplementorInterface.
     */
    function enableCache($enableCache);

    /**
     * Informe about the cache activation.
     *
     * @return bool true if the cache is enabled.
     */
    function isCacheEnabled();
}
