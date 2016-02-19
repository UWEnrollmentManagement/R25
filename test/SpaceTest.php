<?php

namespace UWDOEM\Space\Test;

use PHPUnit_Framework_TestCase;

class SpaceTest extends PHPUnit_Framework_TestCase
{

    /**
     * The consumer shall be able to retrieve a collection of all available spaces.
     */
    public function testGetSpaces()
    {

        define('R25_BASE_PATH', 'https://r25web.admin.washington.edu/r25ws/servlet/wrd/run/');

        define('UW_WS_SSL_KEY_PATH', 'test\\test-certs\\self.signed.test.certs.key');
        define('UW_WS_SSL_CERT_PATH', 'test\\test-certs\\self.signed.test.certs.crt');
        define('UW_WS_SSL_KEY_PASSWD', 'self-signed-password');

        $this->assertEquals(1939, sizeof(MockSpace::getSpaces()));
    }

    /**
     * The consumer shall be able to get detailed space information from a space_id
     */
    public function testGetSpaceFromId()
    {
        $spaceId = 4633;
        $space = MockSpace::fromSpaceId($spaceId);

        $this->assertEquals('BAG  131', $space->getAttribute('space_name'));
    }

    /**
     * The consumer shall be able to retrieve and set space attributes.
     */
    public function testSetGetAttr()
    {
        $p = new MockSpace();

        $p->setAttribute("key1", "value1");
        $p->setAttribute("key2", "value2");

        $this->assertEquals($p->getAttribute("key1"), "value1");
        $this->assertEquals($p->getAttribute("key2"), "value2");
    }

    /**
     * The consumer shall be able to get a collection of available hours from a space.
     */
    public function testGetHours()
    {
        $spaceId = 4633;
        $space = MockSpace::fromSpaceId($spaceId);

        $hours = $space->getHours();

        $this->assertEquals('Friday', $hours[4]['day_name']);
        $this->assertEquals('07:00:00', $hours[4]['open']);
        $this->assertEquals('22:00:00', $hours[4]['close']);

    }

    /**
     * The consumer shall be able to get a collection of features from a space.
     */
    public function testGetFeatures()
    {
        $spaceId = 4633;
        $space = MockSpace::fromSpaceId($spaceId);

        $features = $space->getFeatures();

        $this->assertEquals('Carpeting', $features[3]['feature_name']);
        $this->assertEquals('1', $features[3]['quantity']);
    }

    /**
     * The consumer shall be able to get a collection of custom attributes from a space.
     */
    public function testGetCustomAttributes()
    {
        $spaceId = 4633;
        $space = MockSpace::fromSpaceId($spaceId);

        $customAttributes = $space->getCustomAttributes();

        $this->assertEquals('X25 Owner Organization', $customAttributes[3]['attribute_name']);
        $this->assertEquals('4211', $customAttributes[3]['attribute_value']);
    }
}
