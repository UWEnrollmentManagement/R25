<?php

namespace UWDOEM\Space\Test;

use PHPUnit_Framework_TestCase;
use UWDOEM\Space\Space;

class SpaceTest extends PHPUnit_Framework_TestCase
{

    public function testConnection()
    {

        define('R25_BASE_PATH', 'https://r25web.admin.washington.edu/r25ws/servlet/wrd/run/');

        define('UW_WS_SSL_KEY_PATH', 'test\\test-certs\\self.signed.test.certs.key');
        define('UW_WS_SSL_CERT_PATH', 'test\\test-certs\\self.signed.test.certs.crt');
        define('UW_WS_SSL_KEY_PASSWD', 'self-signed-password');

        $this->assertEquals(1939, sizeof(MockSpace::getSpaces()));
    }

    public function testSetGetAttr()
    {
        $p = new MockSpace();

        $p->setAttr("key1", "value1");
        $p->setAttr("key2", "value2");

        $this->assertEquals($p->getAttr("key1"), "value1");
        $this->assertEquals($p->getAttr("key2"), "value2");
    }
}
