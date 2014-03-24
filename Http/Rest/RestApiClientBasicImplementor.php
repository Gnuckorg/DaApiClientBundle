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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Da\ApiClientBundle\Logger\HttpLoggerInterface;
use Da\ApiClientBundle\Http\Transport\HttpTransportFactory;
use Da\AuthCommonBundle\Exception\ApiHttpResponseException;

/**
 * RestApiClientBasicImplementor is a basic implementation for a REST API client.
 *
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
class RestApiClientBasicImplementor extends AbstractRestApiClientImplementor
{
    protected $container;

    /**
     * Constructor.
     */
    public function __construct(ContainerInterface $container)
    {
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
        $transport = HttpTransportFactory::build('curl', $this->getLogger());

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
        $transport = HttpTransportFactory::build('curl', $this->getLogger());

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
        $transport = HttpTransportFactory::build('curl', $this->getLogger());

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
        $transport = HttpTransportFactory::build('curl', $this->getLogger());

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
        $transport = HttpTransportFactory::build('curl', $this->getLogger());

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
        $transport = HttpTransportFactory::build('curl', $this->getLogger());

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
