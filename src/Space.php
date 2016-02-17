<?php

namespace UWDOEM\Space;

use UWDOEM\Connection\Connection;

/**
 * Container class for data received from Space Web Service
 *
 * @package UWDOEM\Space
 */
class Space
{

    /** @var array */
    protected $attrs = [];

    /** @var \UWDOEM\Connection\Connection */
    protected static $connection;

    /**
     * Sets a value on your LOCAL COPY of the space.
     *
     * SWS/PWS do not support UPDATING space/student models.
     * @param string $key
     * @param mixed  $value
     * @return void
     */
    public function setAttr($key, $value)
    {
        $this->attrs[$key] = $value;
    }

    /**
     * Gets a value from your local copy of the space.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttr($key)
    {
        return $this->attrs[$key];
    }

    /**
     * Returns an arry of the affiliate's attributes.
     *
     * @return array
     */
    public function getAttrs()
    {
        return $this->attrs;
    }

    /**
     * @return Space
     */
    public static function getSpaces()
    {
        $resp = static::getConnection()->execGET(
            "spaces.xml"
        );

        $reader = new \XMLReader();
        $reader->xml($resp);

        /** @var Space $spaces */
        $spaces = [];

        while ($reader->read() === true) {
            if ($reader->name === "r25:space") {
                do {
                    $outerXml = $reader->readOuterXml();

                    if (trim($outerXml) !== '') {
                        /** @var \XMLReader $spaceXml */
                        $spaceXml = new \XMLReader();

                        $spaceXml->xml($outerXml);

                        $spaces[] = static::fromXMLReader($spaceXml);
                    }

                } while ($reader->next() === true);
            }
        }

        return $spaces;
    }

    /**
     * @param \XMLReader $spaceXml
     * @return Space
     */
    protected static function fromXMLReader(\XMLReader $spaceXml)
    {
        $space = new static();

        $attrs = [];
        while ($spaceXml->read() === true) {
            $name = str_replace('r25:', '', $spaceXml->name);

            if ($name !== '' && $name !== '#text' && $name !== 'space') {
                $spaceXml->read();

                if (trim($spaceXml->value) !== '') {
                    $attrs[$name] = $spaceXml->value;
                }
            }
        }
        $space->attrs = $attrs;

        return $space;
    }

    /**
     * @param integer $spaceId
     * @return Space
     */
    public static function fromSpaceId($spaceId)
    {
        $resp = static::getConnection()->execGET(
            "space.xml?space_id=$spaceId"
        );

        $reader = new \XMLReader();
        $reader->xml($resp);

        return static::fromXMLReader($reader);
    }

    /**
     * @return Connection
     * @throws \Exception If any of the required constants have not been set.
     */
    protected static function makeConnection()
    {
        $requiredConstants = ["R25_BASE_PATH", "UW_WS_SSL_KEY_PATH", "UW_WS_SSL_CERT_PATH", "UW_WS_SSL_KEY_PASSWD"];

        foreach ($requiredConstants as $constant) {
            if (defined($constant) === false) {
                throw new \Exception("You must define the constant $constant before using this library.");
            }
        }

        return new Connection(
            R25_BASE_PATH,
            UW_WS_SSL_KEY_PATH,
            UW_WS_SSL_CERT_PATH,
            UW_WS_SSL_KEY_PASSWD
        );
    }

    /**
     * @return Connection
     */
    protected static function getConnection()
    {
        if (static::$connection === null) {
            static::$connection = static::makeConnection();
        }
        return static::$connection;
    }
}
