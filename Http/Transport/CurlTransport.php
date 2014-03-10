<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\Http\Transport;

use Da\ApiClientBundle\Http\Response;

/**
 * CurlTransport.
 *
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
class CurlTransport extends AbstractHttpTransport
{
    const USER_AGENT_NAME = "DaApiClient php/curl/REST-UA";

    protected $cUrl;

    /**
     * {@inheritdoc}
     */
    protected function __construct(RestLoggerInterface $logger = null)
    {
        parent::__construct();

        $this->cUrl = curl_init();
        $this
            ->addCurlOption(CURLOPT_RETURNTRANSFER, true)
            ->addCurlOption(CURLOPT_HEADER, true)
            ->addCurlOption(CURLOPT_USERAGENT, $this->getUserAgent())
        ;
    }

    /**
     * Add cUrl option
     *
     * @param string $key
     * @param mixed $value
     *
     * @return CurlTransport
     */
    protected function addCurlOption($key, $value)
    {
        curl_setopt($this->cUrl, $key, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildRequest()
    {
        $buildRequestMethod = sprintf('build%sRequest', ucfirst(strtolower($this->getMethod)));
        $this
            ->addCurlOption(CURLOPT_URL, $this->getPath())
            ->buildHeaders()
            ->$buildRequestMethod()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildHeaders()
    {
        $headers = array();
        foreach ($this->getHeaders() as $name => $value) {
            $headers[] = sprintf('%s: %s', $name, $value);
        }
        $this->addCurlOption(CURLOPT_HTTPHEADER, $headers);

        return $this;
    }

    /**
     * Build get request
     *
     * @return CurlTransport
     */
    protected function buildGetRequest()
    {
        return $this;
    }

    /**
     * Build post request
     *
     * @return CurlTransport
     */
    protected function buildPostRequest()
    {
        $this
            ->addCurlOption(CURLOPT_POST, true)
            ->addCurlOption(CURLOPT_POSTFIELDS, $this->getQueryStrings())
        ;

        return $this;
    }

    /**
     * Build put request
     *
     * @return CurlTransport
     */
    protected function buildPutRequest()
    {
        $this
            ->addCurlOption(CURLOPT_POST, true)
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, 'PUT')
            ->addCurlOption(CURLOPT_POSTFIELDS, http_build_query($this->getQueryStrings()))
            ->addHeader('X-HTTP-Method-Override', 'PUT')
        ;

        return $this;
    }

    /**
     * Build delete request
     *
     * @return CurlTransport
     */
    protected function buildDeleteRequest()
    {
        $this
            ->addCurlOption(CURLOPT_POST, true)
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, 'DELETE')
            ->addCurlOption(CURLOPT_POSTFIELDS, http_build_query($this->getQueryStrings()))
            ->addHeader('X-HTTP-Method-Override', 'DELETE')
        ;

        return $this;
    }

    /**
     * Build link request
     *
     * @return CurlTransport
     */
    protected function buildLinkRequest()
    {
        $this
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, 'LINK')
            ->addHeader('Link', implode(', ', $this->getLinks()))
            ->addHeader('X-HTTP-Method-Override', 'LINK')
        ;

        return $this;
    }

    /**
     * Build unlink request
     *
     * @return CurlTransport
     */
    protected function buildUnlinkRequest()
    {
        $this
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, 'UNLINK')
            ->addHeader('Link', implode(', ', $this->getLinks()))
            ->addHeader('X-HTTP-Method-Override', 'UNLINK')
        ;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function executeRequest()
    {
        $content = curl_exec($this->cUrl);
        $url = curl_getinfo($cUrl, CURLINFO_EFFECTIVE_URL);
        $code = curl_getinfo($cUrl, CURLINFO_HTTP_CODE);
        die('DIPLAY HEADER NOW !');
        $headers = array(); //TODO: retrieve response headers from curl !
        curl_close($this->cUrl);

        return Reponse::create($content, $url, $code, $headers);
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

        if ('dev' === $this->environment) {
            $this->getLogger()->startQuery(
                curl_getinfo($cUrl, CURLINFO_EFFECTIVE_URL),
                $method,
                $queryString
            );
        }

        $httpContent = curl_exec($cUrl);

        $path = curl_getinfo($cUrl, CURLINFO_EFFECTIVE_URL);
        $httpCode = curl_getinfo($cUrl, CURLINFO_HTTP_CODE);

        if ('dev' === $this->environment) {
            $this->getLogger()->stopQuery($httpCode, $httpContent);
        }

        if ($httpCode >= 400) {
            throw new ApiHttpResponseException($path, $httpCode, $httpContent);
        }

        return $httpContent;
    }
}
