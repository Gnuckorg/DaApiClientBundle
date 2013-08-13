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

use Da\ApiClientBundle\Exception\ApiHttpResponseException;

/**
 * RestApiClientBasicImplementor is a basic implementation for a REST API client.
 *
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
class RestApiClientBasicImplementor extends AbstractRestApiClientImplementor
{
    const USER_AGENT_NAME = "RestApiClientBasic php/curl/REST-UA";

    /**
     * Constructor.
     */
    public function __construct()
    {
        // TODO: pass the arguments you need with the dependency injection.
    }

    /**
     * {@inheritdoc}
     */
    public function get($path, $queryString = null)
    {
        $cUrl = self::initCurl($this->getApiEndpointPath($path));

        return self::execute($cUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function post($path, $queryString = null)
    {
        $cUrl = self::initCurl($this->getApiEndpointPath($path));
        curl_setopt($cUrl, CURLOPT_POST, true);
        curl_setopt($cUrl, CURLOPT_POSTFIELDS, $queryString);

        return self::execute($cUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function put($path, $queryString = null)
    {
        // TODO: implements.
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path, $queryString = null)
    {
        // TODO: implements.
    }

    /**
     * Get the api endpoint path
     *
     * @param string $path
     * @return string The api path (url)
     */
    protected function getApiEndpointPath($path)
    {
        return sprintf('%s%s',
            $this->getEndpointRoot(),
            $path
        );
    }

    /**
     * Init cUrl
     *
     * @param string $path
     * @return cUrl
     */
    protected static function initCurl($path)
    {
        $cUrl = curl_init();
        curl_setopt($cUrl, CURLOPT_URL, $path);
        curl_setopt($cUrl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cUrl, CURLOPT_USERAGENT, self::USER_AGENT_NAME);

        return $cUrl;
    }

    /**
     * Execute cUrl
     *
     * @param cUrl $cUrl
     * @return string
     * @throw ApiHttpResponseException
     */
    protected static function execute($cUrl)
    {
        $data = curl_exec($cUrl);
        $path = curl_getinfo($cUrl, CURLINFO_EFFECTIVE_URL);
        $httpCode = curl_getinfo($cUrl, CURLINFO_HTTP_CODE);
        curl_close($cUrl);

        if($httpCode != 200) {
            throw new ApiHttpResponseException($path, $httpCode, $data);
        }

        return $data;
    }
}
