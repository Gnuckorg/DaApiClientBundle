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

    protected $cUrl;
    protected $logger;

    /**
     * Constructor.
     */
    public function __construct(\Da\ApiClientBundle\Logging\RestLoggerInterface $logger)
    {
        $this->cUrl = null;
        $this->logger = $logger;
    }

    /**
     * Get Logger
     *
     * @return Da\ApiClientBundle\Logging\RestLogger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Add query string
     *
     * @param string $path
     * @param array|string $queryString
     * @return string
     */
    public static function addQueryString($path, $queryString)
    {
        if(null === $queryString) {
            return $path;
        }

        return sprintf("%s%s%s",
            $path,
            preg_match("#\?#", $path) ? (
                preg_match("#\?$#", $path) ?
                '' : '&'
            ) : '?',
            http_build_query($queryString)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function get($path, $queryString = null)
    {
        $path = self::addQueryString($path, $queryString);

        return $this
            ->initCurl($path)
            ->execute($queryString, 'GET')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function post($path, $queryString = null)
    {
        return $this
            ->initCurl($path)
            ->addCurlOption(CURLOPT_POST, true)
            ->addCurlOption(CURLOPT_POSTFIELDS, $queryString)
            ->execute($queryString, 'POST')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function put($path, $queryString = null)
    {
        return $this
            ->initCurl($path)
            ->addCurlOption(CURLOPT_POST, true)
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, "PUT")
            ->addCurlOption(CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: PUT'))
            ->addCurlOption(CURLOPT_POSTFIELDS, $queryString)
            ->execute($queryString, 'PUT')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path, $queryString = null)
    {
        $path = self::addQueryString($path, $queryString);

        return $this
            ->initCurl($path)
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, "DELETE")
            ->addCurlOption(CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: DELETE'))
            ->execute($queryString, 'DELETE')
        ;
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
     * @return RestApiClientBasicImplementor
     */
    protected function initCurl($path)
    {
        $this->cUrl = curl_init();
        $this
            ->addCurlOption(CURLOPT_URL, $this->getApiEndpointPath($path))
            ->addCurlOption(CURLOPT_RETURNTRANSFER, true)
            ->addCurlOption(CURLOPT_USERAGENT, self::USER_AGENT_NAME)
        ;

        if($this->hasSecurityToken()) {
            $this->addCurlOption(CURLOPT_HTTPHEADER, array(sprintf(
                'X-API-Security-Token: %s',
                $this->getSecurityToken()
            )));
        }

        return $this;
    }

    /**
     * Add cUrl option
     *
     * @param string $key
     * @param mixed $value 
     * @return RestApiClientBasicImplementor
     */
    protected function addCurlOption($key, $value)
    {
        curl_setopt($this->cUrl, $key, $value);

        return $this;
    }

    /**
     * Execute cUrl
     *
     * @param string|array $queryString
     * @param string $method
     * @return string
     * @throw ApiHttpResponseException
     */
    protected function execute($queryString = null, $method = null)
    {
        $this->getLogger()->startQuery(
            curl_getinfo($this->cUrl, CURLINFO_EFFECTIVE_URL),
            $method,
            $queryString
        );

        $httpContent = curl_exec($this->cUrl);

        $path = curl_getinfo($this->cUrl, CURLINFO_EFFECTIVE_URL);
        $httpCode = curl_getinfo($this->cUrl, CURLINFO_HTTP_CODE);
        curl_close($this->cUrl);

        $this->getLogger()->stopQuery($httpCode, $httpContent);

        if($httpCode >= 400) {
            throw new ApiHttpResponseException($path, $httpCode, $httpContent);
        }

        return $httpContent;
    }
}
