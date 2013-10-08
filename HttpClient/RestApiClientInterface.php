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
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
interface RestApiClientInterface
{
    /**
     * Get the api endpoint root
     *
     * @return string
     */
    public function getEndpointRoot();

    /**
     * Get
     *
     * @param string $path       The relative path to the webservice.
     * @param array $queryString The specific queryString to the webservice.
     *
     * @return string
     *
     * @throw \Da\AuthCommonBundle\Exception\ApiHttpResponseException
     */
    public function get($path, array $queryString = array());

    /**
     * Post
     *
     * @param string $path       The relative path to the webservice.
     * @param array $queryString The specific queryString to the webservice.
     *
     * @return string
     *
     * @throw \Da\AuthCommonBundle\Exception\ApiHttpResponseException
     */
    public function post($path, array $queryString = array());

    /**
     * Put
     *
     * @param string $path       The relative path to the webservice.
     * @param array $queryString The specific queryString to the webservice.
     *
     * @return string
     *
     * @throw \Da\AuthCommonBundle\Exception\ApiHttpResponseException
     */
    public function put($path, array $queryString = array());

    /**
     * Delete
     *
     * @param string $path       The relative path to the webservice.
     * @param array $queryString The specific queryString to the webservice.
     *
     * @return string
     *
     * @throw \Da\AuthCommonBundle\Exception\ApiHttpResponseException
     */
    public function delete($path, array $queryString = array());
}
