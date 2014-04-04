<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
class ApiHttpResponseException  extends \RuntimeException implements HttpExceptionInterface
{
    protected $url;
    protected $code;
    protected $headers;
    protected $body;

    /**
     * Constructor
     *
     * @param string  $url
     * @param integer $code
     * @param array   $headers
     * @param string  $body
     */
    public function __construct($url, $code, $headers, $body)
    {
        $this->url     = $url;
        $this->code    = $code;
        $this->headers = $headers;
        $this->body    = $body;

        parent::__construct(sprintf('HTTP Api response error: (%s) %s',
            $code,
            $url
        ));
    }

    /**
     * GetUrl
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * GetCode
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * GetBody
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
