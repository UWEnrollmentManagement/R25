<?php

namespace UWDOEM\Space;

use UWDOEM\Connection\Connection;

/**
 * Class AbstractEntity encapsulates those methods used by all entities
 * presented by the R25 web service.
 *
 * @package UWDOEM\Space
 */
abstract class AbstractEntity
{

    /** @var array */
    protected $attributes = [];

    /** @var \UWDOEM\Connection\Connection */
    protected static $connection;

    /**
     * Retrieve a collection of entities with the given $elementName at the
     * given $url.
     *
     * @param string $url
     * @param string $elementName
     * @return static[]
     */
    protected static function getCollection($url, $elementName)
    {
        $resp = static::getConnection()->execGET(
            $url
        );

        $reader = new \XMLReader();
        $reader->xml($resp);

        /** @var static[] $entities */
        $entities = [];

        while ($reader->read() === true) {
            if ($reader->name === "r25:$elementName") {
                do {
                    $outerXml = $reader->readOuterXml();

                    if (trim($outerXml) !== '') {
                        $entities[] = static::fromXMLString($outerXml, $elementName);
                    }

                } while ($reader->next() === true);
            }
        }

        return $entities;
    }

    /**
     * Sets a value on your LOCAL COPY of the space.
     *
     * SWS/PWS do not support UPDATING space/student models.
     * @param string $key
     * @param mixed  $value
     * @return void
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Gets a value from your local copy of the space.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        return $this->attributes[$key];
    }

    /**
     * Returns an arry of the affiliate's attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Create an entity from
     * @param string $spaceXml
     * @param string $elementName
     * @return static
     */
    protected static function fromXMLString($spaceXml, $elementName)
    {
        /** @var static $entity */
        $entity = new static();

        /** @var mixed[] $rawAttributes */
        $rawAttributes = [];

        $parser = xml_parser_create();

        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);

        xml_parse_into_struct($parser, $spaceXml, $rawAttributes);

        $attributes = static::parseXmlStruct($rawAttributes);

        if (array_key_exists($elementName, $attributes) === true) {
            $attributes = $attributes[$elementName];
        }

        $entity->attributes = $attributes;

        return $entity;
    }

    /**
     * Adds an attribute to a collection of attributes.
     *
     * If this attribute already exists in the collection, then this method will
     * turn that attribute into a collection of attributes.
     *
     * @param mixed[] $attributes
     * @param string  $attributeName
     * @param mixed   $attribute
     * @return mixed[]
     */
    protected static function addAttribute(array $attributes, $attributeName, $attribute)
    {
        if (array_key_exists($attributeName, $attributes) === true) {
            if (
                is_array($attributes[$attributeName]) === true
                && array_key_exists(0, $attributes[$attributeName]) === true
            ) {
                $attributes[$attributeName][] = $attribute;
            } else {
                $attributes[$attributeName] = [$attributes[$attributeName], $attribute];
            }
        } else {
            $attributes[$attributeName] = $attribute;
        }

        return $attributes;
    }

    /**
     * Create an attribute collection from an XML struct.
     *
     * Takes a mixed[] structure such as that produced by xml_parse_into_struct.
     *
     * @param mixed[] $struct
     * @return mixed[]
     */
    protected static function parseXmlStruct(array $struct)
    {
        $attributes = [];

        while (next($struct) !== false) {

            /** @var mixed[] $rawAttribute */
            $rawAttribute = current($struct);

            /** @var boolean $hasValue */
            $hasValue = array_key_exists('value', $rawAttribute);

            /** @var boolean $isOpenTag */
            $isOpenTag = $rawAttribute['type'] === 'open';

            /** @var string $unqualifiedName */
            $unqualifiedName = str_replace('r25:', '', $rawAttribute['tag']);

            if ($hasValue === true) {
                $attributes = static::addAttribute($attributes, $unqualifiedName, $rawAttribute['value']);
            } elseif ($isOpenTag === true) {
                $thisLevel = current($struct)['level'];

                $childElements = [];
                while (next($struct) !== false && current($struct)['level'] > $thisLevel) {
                    $childElements[] = current($struct);
                }

                $attributes = static::addAttribute(
                    $attributes,
                    $unqualifiedName,
                    static::parseXmlStruct($childElements)
                );
            }
        }

        return $attributes;
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
