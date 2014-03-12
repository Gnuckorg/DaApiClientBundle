<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\Http\ApiClient;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Da\ApiClientBundle\Http\Logger\RestLoggerInterface;
use Da\ApiClientBundle\Http\Transport\HttpTransportFactory;
use Da\AuthCommonBundle\Exception\ApiHttpResponseException;

/**
 * RestApiClientBasicImplementor is a basic implementation for a REST API client.
 *
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
class RestApiClientBasicImplementor extends AbstractRestApiClientImplementor
{
    protected $isFirstTry;
    protected $container;

    /**
     * Constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->isFirstTry = true;
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
        $logger = null;
        $transport = HttpTransportFactory::build('curl', $logger);

        return $transport
            ->setMethod('GET')
            ->setPath($this->getApiEndpointPath($path))
            ->setQueryStrings($queryString)
            ->setHeaders($headers)
            ->send()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function post($path, array $queryString = array(), array $headers = array())
    {
        $transport = HttpTransportFactory::build('curl', $logger);

        return $transport
            ->setMethod('POST')
            ->setPath($this->getApiEndpointPath($path))
            ->setQueryStrings($queryString)
            ->setHeaders($headers)
            ->send()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function put($path, array $queryString = array(), array $headers = array())
    {
        $transport = HttpTransportFactory::build('curl', $logger);

        return $transport
            ->setMethod('PUT')
            ->setPath($this->getApiEndpointPath($path))
            ->setQueryStrings($queryString)
            ->setHeaders($headers)
            ->send()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path, array $queryString = array(), array $headers = array())
    {
        $transport = HttpTransportFactory::build('curl', $logger);

        return $transport
            ->setMethod('DELETE')
            ->setPath($this->getApiEndpointPath($path))
            ->setQueryStrings($queryString)
            ->setHeaders($headers)
            ->send()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function link($path, array $links, array $headers = array())
    {
        $transport = HttpTransportFactory::build('curl', $logger);

        return $transport
            ->setMethod('LINK')
            ->setPath($this->getApiEndpointPath($path))
            ->setLinks($links)
            ->setHeaders($headers)
            ->send()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function unlink($path, array $links, array $headers = array())
    {
        $transport = HttpTransportFactory::build('curl', $logger);

        return $transport
            ->setMethod('UNLINK')
            ->setPath($this->getApiEndpointPath($path))
            ->setLinks($links)
            ->setHeaders($headers)
            ->send()
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
}
