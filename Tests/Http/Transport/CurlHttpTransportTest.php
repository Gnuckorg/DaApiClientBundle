<?php

namespace Da\ApiClientBundle\Tests\Http\Transport;

use Da\ApiClientBundle\Http\Transport\CurlHttpTransport;

class CurlHttpTransportTest extends \PHPUnit_Framework_TestCase
{
    public function testhttp_build_query_for_curl()
    {
        $arr1 = array('a' => 'a', 'b' => 'b');
        $this->assertEquals(
            CurlHttpTransport::http_build_query_for_curl($arr1),
            $arr1
        );

        $arr2 = array('a' => 'a', 'n' => null);
        $this->assertEquals(
            CurlHttpTransport::http_build_query_for_curl($arr2),
            $arr2
        );

        $arr3 = array('a' => 'a', 'm' => array('m1' => 'vm1'));
        $this->assertEquals(
            CurlHttpTransport::http_build_query_for_curl($arr3),
            array('a' => 'a', 'm[m1]' => 'vm1')
        );
    }
}