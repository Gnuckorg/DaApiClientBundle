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

/**
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
class ApiHttpResponseException extends \Exception
{
    protected $httpCode;

    /**
     * Constructor
     *
     * @param string $url
     * @param string $httpCode
     * @param string $content
     */
    public function __construct($url, $httpCode, $content)
    {
        $this->setHttpCode($httpCode);

        parent::__construct(
            sprintf(
                "%s\nApi response error code: %d\n%s",
                $url,
                $httpCode,
                $content
            ),
            0,
            null
        );
    }

    /**
     * Set Http Code
     *
     * @param integer $code
     */
    public function setHttpCode($code)
    {
        $this->httpCode = $code;
    }

    /**
     * Get Http Code
     *
     * @return integer
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }
}
