<?php

namespace Okxe\Elasticsearch\Abstracts;

use Okxe\Elasticsearch\Traits\BodyTrait;

/**
 * Base class for indexes.
 */
abstract class AbstractIndex
{
    use BodyTrait;

    /**
     * @return string
     */
    abstract public function getName();

    /**
     * @return array
     */
    abstract public function setup();

    /**
     */
    public function __construct()
    {
        $this->setup();
    }

    /**
     * The name used to communicate with the elasticsearch server. Can be used to
     * add a prefix but still use the name to refer to it.
     *
     * @return string
     */
    public function getInternalName()
    {
        return $this->getName();
    }

    /**
     * @return array
     */
    public function getBody()
    {
        // Replace fragments with their raw body.
        return $this->body;
    }

    /**
     * @param array $mappings
     *
     * @return array
     */
    public function setMappings(array $mappings)
    {
        return $this->set('mappings', $mappings);
    }

    /**
     * @return array
     */
    public function getMappings()
    {
        return $this->get('mappings');
    }

    /**
     * @param array $settings
     *
     * @return array
     */
    public function setSettings(array $settings)
    {
        return $this->set('settings', $settings);
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->get('settings');
    }
}
