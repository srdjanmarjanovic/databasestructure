<?php

namespace ActiveCollab\DatabaseStructure\Trigger;

use ActiveCollab\DatabaseStructure\Trigger;

/**
 * @package ActiveCollab\DatabaseStructure\Trigger
 */
class AfterInsertTrigger extends Trigger
{
    /**
     * Return trigger time (before or after)
     *
     * @return string
     */
    public function getTime()
    {
        return self::AFTER;
    }

    /**
     * Return trigger event (insert, update or delete)
     *
     * @return string
     */
    public function getEvent()
    {
        return self::INSERT;
    }
}