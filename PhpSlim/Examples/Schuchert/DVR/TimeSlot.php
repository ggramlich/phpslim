<?php
class Schuchert_DVR_TimeSlot
{
    public $channel;
    public $startDateTime;
    public $durationInMinutes;

    public function __construct($channel, $startDateTime, $durationInMinutes)
    {
        $this->channel = $channel;
        $this->startDateTime = $startDateTime;
        $this->durationInMinutes = $durationInMinutes;
    }
 
    public function conflictsWith($other)
    {
        if (
            $this->channel == $other->channel
            && $this->startDateTime == $other->startDateTime) {
           return true;
        }
        return false;
    }
}

