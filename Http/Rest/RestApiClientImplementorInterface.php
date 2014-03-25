<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\Http\Rest;

use Da\AuthCommonBundle\Security\AuthorizationRefresherInterface;
use Doctrine\Common\Cache\Cache;
use Da\ApiClientBundle\Logger\HttpLoggerInterface;

/**
 * RestApiClientImplementorInterface is the interface that an RestApiClientImplementor
 * should implement to be used as an implementor by the RestApiClientBridge.
 *
 * @author Thomas Prelot <tprelot@gmail.com>
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
interface RestApiClientImplementorInterface extends RestApiClientInterface
{
    /**
     * Set the endpoint root URL of the API (from which all the path will be relative to).
     *
     * @param string $endpointRoot The api endpoint root URL.
     *
     * @return RestApiClientImplementorInterface.
     */
    public function setEndpointRoot($endpointRoot);

    /**
     * Get the security token to authenticate your client in the API.
     *
     * @return string The security token.
     */
    public function getSecurityToken();

    /**
     * Set the security token to authenticate your client in the API.
     *
     * @param string $securityToken The security token.
     *
     * @return RestApiClientImplementorInterface.
     */
    public function setSecurityToken($securityToken);

    /**
     * Check if the security token is present
     *
     * @return boolean true if the token is set.
     */
    public function hasSecurityToken();

    /**
     * Get the api cacher
     *
     * @return Cache|null.
     */
    public function getCacher();

    /**
     * Set the cacher to cache rest api request
     *
     * @param Cache $cacher The cacher to cache rest api
     *
     * @return RestApiClientImplementorInterface.
     */
    public function setCacher(Cache $cacher);

    /**
     * Is cache enabled
     *
     * @return boolean.
     */
    public function isCacheEnabled();

    /**
     * Set cache enabled
     *
     * @param boolean $cacheEnabled
     *
     * @return RestApiClientImplementorInterface.
     */
    public function setCacheEnabled($cacheEnabled);

    /**
     * Get the api logger
     *
     * @return HttpLoggerInterface|null.
     */
    public function getLogger();

    /**
     * Set the logger to log rest api request
     *
     * @param HttpLoggerInterface $logger The logger to log rest api
     *
     * @return RestApiClientImplementorInterface.
     */
    public function setLogger(HttpLoggerInterface $logger);

    /**
     * Is log enabled
     *
     * @return boolean.
     */
    public function isLogEnabled();

    /**
     * Set log enabled
     *
     * @param boolean $logEnabled
     *
     * @return RestApiClientImplementorInterface.
     */
    public function setLogEnabled($logEnabled);
}
