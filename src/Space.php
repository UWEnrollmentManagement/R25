<?php

namespace UWDOEM\Space;

/**
 * Container class for data received from Space Web Service
 *
 * @package UWDOEM\Space
 */
class Space extends AbstractEntity
{
    
    /**
     * @return array
     */
    public function getHours()
    {
        return $this->getPluralAttribute('hours');
    }

    /**
     * @return array
     */
    public function getCustomAttributes()
    {
        return $this->getPluralAttribute('custom_attribute');
    }

    /**
     * @return array
     */
    public function getFeatures()
    {
        return $this->getPluralAttribute('feature');
    }

    /**
     * @param string $attributeName
     * @return array
     */
    protected function getPluralAttribute($attributeName)
    {
        $attribute = $this->getAttribute($attributeName);

        if (is_array($attribute) !== true) {
            $attribute = [$attribute];
        }

        return $attribute;
    }

    /**
     * @return Space[]
     */
    public static function getSpaces()
    {
        return static::getCollection('spaces.xml', 'space');
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

        return static::fromXMLString($resp, 'space');
    }
}
