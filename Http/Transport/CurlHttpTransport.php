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

use Doctrine\Common\Cache\Cache;
use Da\ApiClientBundle\Logger\HttpLoggerInterface;
use Da\ApiClientBundle\Http\Response;

/**
 * CurlHttpTransport.
 *
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
class CurlHttpTransport extends AbstractHttpTransport
{
    public static $USER_AGENT_NAME = "DaApiClient php/curl/REST-UA";

    protected $cUrl;

    /**
     * {@inheritdoc}
     */
    public function __construct(Cache $cacher = null, HttpLoggerInterface $logger = null)
    {
        parent::__construct($cacher, $logger);

        $this->cUrl = curl_init();
        $this
            ->addCurlOption(CURLOPT_RETURNTRANSFER, true)
            ->addCurlOption(CURLOPT_HEADER, true)
            ->addCurlOption(CURLOPT_SAFE_UPLOAD, true)
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
        $buildRequestMethod = sprintf('build%sRequest', ucfirst(strtolower($this->getMethod())));

        return $this
            ->addCurlOption(CURLOPT_URL, $this->getPath())
            ->$buildRequestMethod()
            ->buildHeaders()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildHeaders()
    {
        $this->addHeader('User-Agent', $this->getUserAgent());
        $headers = array();
        foreach ($this->getHeaders() as $name => $value) {
            $headers[] = sprintf('%s: %s', $name, $value);
        }

        return $this->addCurlOption(CURLOPT_HTTPHEADER, $headers);
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
        return $this
            ->addCurlOption(CURLOPT_POST, true)
            ->addCurlOption(
                CURLOPT_POSTFIELDS,
                is_array($this->getQueryString()) ?
                    self::http_build_query_for_curl($this->getQueryString()) :
                    $this->getQueryString()
            )
        ;
    }

    /**
     * Build put request
     *
     * @return CurlTransport
     */
    protected function buildPutRequest()
    {
        /**
            PHP BUG !
            Use http_build_query and not self::http_build_query_for_curl
            @see https://github.com/symfony/symfony/pull/10381
            @see http://tools.ietf.org/html/draft-ietf-httpbis-p2-semantics-21#section-5.3.4
            @see https://bugs.php.net/bug.php?id=55815
         */
        return $this
            ->addCurlOption(CURLOPT_POST, true)
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, 'PUT')
            ->addCurlOption(
                CURLOPT_POSTFIELDS,
                is_array($this->getQueryString()) ?
                    http_build_query($this->getQueryString()) :
                    $this->getQueryString()
            )
            ->addHeader('X-HTTP-Method-Override', 'PUT')
        ;
    }

    /**
     * Build patch request
     *
     * @return CurlTransport
     */
    protected function buildPatchRequest()
    {
         /**
            PHP BUG !
            Use http_build_query and not self::http_build_query_for_curl
            @see https://github.com/symfony/symfony/pull/10381
            @see http://tools.ietf.org/html/draft-ietf-httpbis-p2-semantics-21#section-5.3.4
            @see https://bugs.php.net/bug.php?id=55815
         */
        return $this
            ->addCurlOption(CURLOPT_POST, true)
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, 'PATCH')
            ->addCurlOption(
                CURLOPT_POSTFIELDS,
                is_array($this->getQueryString()) ?
                    http_build_query($this->getQueryString()) :
                    $this->getQueryString()
            )
            ->addHeader('X-HTTP-Method-Override', 'PATCH')
        ;
    }

    /**
     * Build delete request
     *
     * @return CurlTransport
     */
    protected function buildDeleteRequest()
    {
        /**
            PHP BUG !
            Use http_build_query and not self::http_build_query_for_curl
            @see https://github.com/symfony/symfony/pull/10381
            @see http://tools.ietf.org/html/draft-ietf-httpbis-p2-semantics-21#section-5.3.4
            @see https://bugs.php.net/bug.php?id=55815
         */
        return $this
            ->addCurlOption(CURLOPT_POST, true)
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, 'DELETE')
            ->addCurlOption(
                CURLOPT_POSTFIELDS,
                is_array($this->getQueryString()) ?
                    http_build_query($this->getQueryString()) :
                    $this->getQueryString()
            )
            ->addHeader('X-HTTP-Method-Override', 'DELETE')
        ;
    }

    /**
     * Build link request
     *
     * @return CurlTransport
     */
    protected function buildLinkRequest()
    {
        return $this
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, 'LINK')
            ->addHeader('Link', implode(', ', $this->getLinks()))
            ->addHeader('X-HTTP-Method-Override', 'LINK')
        ;
    }

    /**
     * Build unlink request
     *
     * @return CurlTransport
     */
    protected function buildUnlinkRequest()
    {
        return $this
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, 'UNLINK')
            ->addHeader('Link', implode(', ', $this->getLinks()))
            ->addHeader('X-HTTP-Method-Override', 'UNLINK')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function executeRequest()
    {
        $response = curl_exec($this->cUrl);
        $url = curl_getinfo($this->cUrl, CURLINFO_EFFECTIVE_URL);
        $code = curl_getinfo($this->cUrl, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($this->cUrl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        $body = $body ? $body : null;
        curl_close($this->cUrl);

        return Response::create($url, $body, $code, self::parseHeaders($header));
    }

    /**
     * Parse Headers
     *
     * @param string $raw
     * @return array
     */
    public static function parseHeaders($raw)
    {
        $headers = array(); // $headers = [];

        foreach (explode("\n", $raw) as $i => $h) {
            $h = explode(':', $h, 2);

            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1]);
                } elseif (is_array($headers[$h[0]])) {
                    $tmp = array_merge($headers[$h[0]], array(trim($h[1])));
                    $headers[$h[0]] = $tmp;
                } else {
                    $tmp = array_merge(array($headers[$h[0]]), array(trim($h[1])));
                    $headers[$h[0]] = $tmp;
                }
            }
        }

        return $headers;
    }

    /**
     * Build http query that will be cUrl compliant
     *
     * @param array|object $arrays The data to transform.
     * @param array        $new    The built array.
     * @param string|null  $prefix The key to flatten if the value found is an array or an object.
     *
     * @return array A compliant cUrl data.
     */
    public static function http_build_query_for_curl($arrays, &$new = array(), $prefix = null)
    {
        if ($arrays instanceof \CURLFile) {
            $new[$prefix] = $arrays;

            return;
        }

        if (is_object($arrays)) {
            $arrays = get_object_vars($arrays);
        }

        foreach ($arrays AS $key => $value) {
            $k = isset($prefix) ?
                sprintf('%s[%s]', $prefix, $key) :
                $key;
            if (is_array($value) OR is_object($value)) {
                self::http_build_query_for_curl($value, $new, $k);
            } else {
                $new[$k] = $value;
            }
        }

        return $new;
    }
}
