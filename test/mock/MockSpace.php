<?php

namespace UWDOEM\Space\Test;

use UWDOEM\Space\Space;

class MockSpace extends Space
{
    protected static function makeConnection()
    {
        return new \UWDOEM\Connection\Test\MockConnection(
            "http://localhost/",
            getcwd() . "",
            getcwd() . "/test/test-certs/self.signed.test.certs.crt",
            ''
        );
    }
}
