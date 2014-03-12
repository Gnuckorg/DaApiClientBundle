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
    public function __construct($url, $code, $content)
    {
        parent::__construct(sprintf('HTTP Api response error: (%s) %s %s',
            $code,
            $url,
            $content
        ));
    }
}
