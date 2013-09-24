<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\Logging;

/**
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
interface RestLoggerInterface
{
    /**
     * Logs a REST Api statement somewhere.
     *
     * @param string $endpoint The enpoint to be called.
     * @param array $method The http method used
     * @param array $queryString The api query string.
     */
    public function startQuery($endpoint, $method = null, array $queryString = null);


    /**
     * Mark the last started query as stopped. This can be used for timing of queries.
     *
     * @param int $responseCode The http response code
     * @param string $responseContent The http response content
     */
    public function stopQuery($responseCode, $responseContent);
}