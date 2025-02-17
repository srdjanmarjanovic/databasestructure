<?php

/*
 * This file is part of the Active Collab DatabaseStructure project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseStructure\Behaviour\AdditionalPropertiesInterface;

use ActiveCollab\DatabaseStructure\Behaviour\AdditionalPropertiesInterface;

/**
 * @package ActiveCollab\DatabaseStructure\Behaviour\AdditionalPropertiesInterface
 */
trait Implementation
{
    /**
     * Cached log attributes value.
     *
     * @var array
     */
    private $decoded_additional_properties;

    /**
     * Return additional log properties as array.
     *
     * @return array
     */
    public function getAdditionalProperties(): array
    {
        if ($this->decoded_additional_properties === null) {
            $raw = trim($this->getRawAdditionalProperties());
            $this->decoded_additional_properties = empty($raw) ? [] : json_decode($raw, true);

            if (!is_array($this->decoded_additional_properties)) {
                $this->decoded_additional_properties = [];
            }
        }

        return $this->decoded_additional_properties;
    }

    /**
     * @param  array|null                          $value
     * @return AdditionalPropertiesInterface|$this
     */
    public function &setAdditionalProperties(array $value = null): AdditionalPropertiesInterface
    {
        $this->decoded_additional_properties = null; // Reset...

        $this->setRawAdditionalProperties(json_encode($value));

        return $this;
    }

    /**
     * Returna attribute value.
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getAdditionalProperty(string $name, $default = null)
    {
        $additional_properties = $this->getAdditionalProperties();

        return $additional_properties && isset($additional_properties[$name]) ? $additional_properties[$name] : $default;
    }

    /**
     * Set attribute value.
     *
     * @param  string                              $name
     * @param  mixed                               $value
     * @return $this|AdditionalPropertiesInterface
     */
    public function &setAdditionalProperty(string $name, $value): AdditionalPropertiesInterface
    {
        $additional_properties = $this->getAdditionalProperties();

        if ($value === null) {
            if (isset($additional_properties[$name])) {
                unset($additional_properties[$name]);
            }
        } else {
            $additional_properties[$name] = $value;
        }

        $this->setAdditionalProperties($additional_properties);

        return $this;
    }

    // ---------------------------------------------------
    //  Expectations
    // ---------------------------------------------------

    /**
     * Get raw additional properties value.
     *
     * @return string
     */
    abstract public function getRawAdditionalProperties();

    /**
     * Set raw additional properties value.
     *
     * @param null|string $value
     * @return $this
     */
    abstract public function &setRawAdditionalProperties(?string $value);
}
