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

use Da\ApiClientBundle\Http\Logger\RestLoggerInterface;

/**
 * HttpTransportFactory
 *
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
abstract class HttpTransportFactory
{
    /**
     * Build
     *
     * @param string              $transportName
     * @param RestLoggerInterface $logger
     * @return HttpTransportInterface
     */
    public static function build($transportName, RestLoggerInterface $logger = null)
    {
        $className = sprintf(
            'Da\ApiClientBundle\Http\Transport\%sTransport',
            ucfirst(strtolower($transportName))
        );

        return new $className($logger);
    }
}
