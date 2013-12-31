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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Da\ApiClientBundle\Logging\RestLoggerInterface;
use Da\AuthCommonBundle\Exception\ApiHttpResponseException;

/**
 * RestApiClientBasicImplementor is a basic implementation for a REST API client.
 *
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
class RestApiClientBasicImplementor extends AbstractRestApiClientImplementor
{
    const USER_AGENT_NAME = "RestApiClientBasic php/curl/REST-UA";

    protected $cUrl;
    protected $headers;
    protected $logger;
    protected $container;
    protected $isFirstTry;

    /**
     * Constructor.
     */
    public function __construct(RestLoggerInterface $logger, ContainerInterface $container)
    {
        $this->cUrl = null;
        $this->headers = array();
        $this->isFirstTry = true;
        $this->logger = $logger;
        // Use container to remove annoying circular dependencies.
        $this->container = $container;
    }

    /**
     * Get the access token if it exist or null otherwise.
     *
     * @return string|null The access token.
     */
    protected function getAccessToken()
    {
        $accessToken = null;

        // Use container to remove annoying circular dependencies.
        if ($this->container->has('security.context')) {
            $securityContext = $this->container->get('security.context');
            if (($token = $securityContext->getToken())) {
                $class = new \ReflectionClass($token);

                if ($class->hasMethod('getAccessToken')) {
                    $accessToken = $token->getAccessToken();
                }
            }
        }

        return $accessToken;
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
     * @param array  $queryString
     *
     * @return string
     */
    public static function addQueryString($path, array $queryString = array())
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
    public function get($path, array $queryString = array(), array $headers = array())
    {
        $path = self::addQueryString($path, $queryString);

        return $this
            ->initCurl($path)
            ->initHeaders($headers)
            ->execute($queryString, 'GET')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function post($path, array $queryString = array(), array $headers = array())
    {
        return $this
            ->initCurl($path)
            ->initHeaders($headers)
            ->addCurlOption(CURLOPT_POST, true)
            ->addCurlOption(CURLOPT_POSTFIELDS, $queryString)
            ->execute($queryString, 'POST')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function put($path, array $queryString = array(), array $headers = array())
    {
        return $this
            ->initCurl($path)
            ->initHeaders($headers)
            ->addCurlOption(CURLOPT_POST, true)
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, 'PUT')
            ->setHeader('X-HTTP-Method-Override', 'PUT')
            ->addCurlOption(CURLOPT_POSTFIELDS, http_build_query($queryString))
            ->execute($queryString, 'PUT')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path, array $queryString = array(), array $headers = array())
    {
        return $this
            ->initCurl($path)
            ->initHeaders($headers)
            ->addCurlOption(CURLOPT_POST, true)
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, 'DELETE')
            ->setHeader('X-HTTP-Method-Override', 'DELETE')
            ->addCurlOption(CURLOPT_POSTFIELDS, http_build_query($queryString))
            ->execute($queryString, 'DELETE')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function link($path, array $links, array $headers = array())
    {
        $curl = $this
            ->initCurl($path)
            ->initHeaders($headers)
        ;

        return $curl
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, 'LINK')
            ->setHeader('X-HTTP-Method-Override', 'LINK')
            ->setHeader('Link', implode(', ', $links))
            ->execute(array(), 'LINK')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function unlink($path, array $links, array $headers = array())
    {
        $curl = $this
            ->initCurl($path)
            ->initHeaders($headers)
        ;

        return $curl
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, 'UNLINK')
            ->setHeader('X-HTTP-Method-Override', 'UNLINK')
            ->setHeader('Link', implode(', ', $links))
            ->execute(array(), 'UNLINK')
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
     *
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

        return $this;
    }

    /**
     * Init the headers
     *
     * @param array $headers
     *
     * @return RestApiClientBasicImplementor
     */
    protected function initHeaders(array $headers)
    {
        $this->headers = array();
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }

        return $this;
    }

    /**
     * Set a header
     *
     * @param string $name
     * @param string $value
     *
     * @return RestApiClientBasicImplementor
     */
    protected function setHeader($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Add the headers of the request
     *
     * @param resource $cUrl
     */
    protected function addHeaders($cUrl)
    {
        $headers = array();
        foreach ($this->headers as $name => $value) {
            $headers[] = sprintf('%s: %s', $name, $value);
        }

        $this->addCurlOption(CURLOPT_HTTPHEADER, $headers, $cUrl);
    }

    /**
     * Add the security tokens
     */
    protected function addSecurityTokens()
    {
        // API token.
        if($this->hasSecurityToken()) {
            $this->setHeader('X-API-Security-Token', $this->getSecurityToken());
        }

        // Access token (oauth).
        $accessToken = $this->getAccessToken();
        if ($accessToken) {
            $this->setHeader('Authorization', sprintf('Bearer %s', $accessToken));
        }
    }

    /**
     * Add cUrl option
     *
     * @param string $key
     * @param mixed $value
     * @param resource $cUrl
     *
     * @return RestApiClientBasicImplementor
     */
    protected function addCurlOption($key, $value, $cUrl = null)
    {
        if (!$cUrl) {
            $cUrl = $this->cUrl;
        }
        curl_setopt($cUrl, $key, $value);

        return $this;
    }

    /**
     * Execute cUrl
     *
     * @param array  $queryString
     * @param string $method
     *
     * @return string
     *
     * @throw ApiHttpResponseException
     */
    protected function execute(array $queryString = array(), $method = null)
    {
        $httpContent = '';

        try {
            try {
                $httpContent = $this->tryExecution($this->cUrl, $queryString, $method);
            } catch (ApiHttpResponseException $exception) {
                $exception->setFirstTry($this->isFirstTry);
                throw $exception;
            }
        } catch (\Exception $e) {
            $this->isFirstTry = false;

            if (is_resource($this->cUrl)) {
                curl_close($this->cUrl);
            }

            throw $e;
        }

        if (is_resource($this->cUrl)) {
            curl_close($this->cUrl);
        }

        return $httpContent;
    }

    /**
     * Execute cUrl
     *
     * @param resource $cUrl
     * @param array    $queryString
     * @param string   $method
     *
     * @return string
     *
     * @throw ApiHttpResponseException
     */
    protected function tryExecution($cUrl, array $queryString = array(), $method = null)
    {
        $this->addSecurityTokens();
        $this->addHeaders($cUrl);

        $this->getLogger()->startQuery(
            curl_getinfo($cUrl, CURLINFO_EFFECTIVE_URL),
            $method,
            $queryString
        );

        $httpContent = curl_exec($cUrl);

        $path = curl_getinfo($cUrl, CURLINFO_EFFECTIVE_URL);
        $httpCode = curl_getinfo($cUrl, CURLINFO_HTTP_CODE);

        $this->getLogger()->stopQuery($httpCode, $httpContent);

        if ($httpCode >= 400) {
            throw new ApiHttpResponseException($path, $httpCode, $httpContent);
        }

        return $httpContent;
    }
}
