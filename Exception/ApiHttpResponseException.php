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
 * @author Thomas Prelot <tprelot@gmail.com>
 */
class ApiHttpResponseException extends \RuntimeException implements HttpExceptionInterface
{
    protected $httpCode;
    protected $headers;
    protected $jsonMessage;

    /**
     * Constructor
     *
     * @param string $url
     * @param string $httpCode
     * @param string $content
     */
    public function __construct($url, $httpCode, $content, $headers = array())
    {
        $this->httpCode = $httpCode;
        $this->headers = $headers;
        $stringContent = $content;

        try {
            $content = json_decode($content);
        }
        catch (\Exception $e) {
        }

        $this->jsonMessage = json_encode(
            array(
                'message' => sprintf(
                    'The API \'%s\' returned an error.',
                    $url
                ),
                'http_code' => $httpCode,
                'error' => $content
            ),
            JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT
        );

        parent::__construct(
            sprintf(
                "%s\nApi response error code: %d\n%s",
                $url,
                $httpCode,
                $stringContent
            ),
            0,
            null
        );
    }

    /**
     * Get the HTTP Code.
     *
     * @return integer The code.
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * {@inheritDoc}
     */
    public function getStatusCode()
    {
        return $this->getHttpCode();
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get the message in a json format.
     *
     * @return string The json message.
     */
    public function getJsonMessage()
    {
        return $this->jsonMessage;
    }
}
