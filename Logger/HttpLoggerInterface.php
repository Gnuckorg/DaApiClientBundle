<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\Logger;

/**
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
interface HttpLoggerInterface
{
    /**
     * Logs a REST Api statement somewhere.
     *
     * @param string $requestUrl         The http request enpoint to call
     * @param string $requestMethod      The http request method to use
     * @param array  $requestHeaders     The http request headers.
     * @param array  $requestQueryString The http request query string
     *
     * @return int a log id
     */
    public function startQuery($requestUrl, $requestMethod, $requestHeaders = array(), array $requestQueryString = null);

    /**
     * Mark the last started query as stopped. This can be used for timing of queries.
     *
     * @param int    $id              The log id to stop
     * @param int    $responseCode    The http response code
     * @param string $responseHeaders The http response headers
     * @param string $responseContent The http response content
     */
    public function stopQuery($id, $responseCode, $responseHeaders, $responseContent);
}
