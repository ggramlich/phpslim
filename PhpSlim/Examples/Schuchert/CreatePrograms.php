<?php
class Schuchert_CreatePrograms
{
    private $_name;
    private $_channel;
 
    public function setName($name)
    {
        $this->_name = $name;
    }
 
    public function setChannel($channel)
    {
        $this->_channel = $channel;
    }
 
    public function setDayOfWeek($dayOfWeek)
    {
    }
 
    public function setTimeOfDay($timeOfDay)
    {
    }
 
    public function setDurationInMinutes($durationInMinutes)
    {
    }
 
    public function id()
    {
        return sprintf("[%s:%d]", $this->_name, $this->_channel);
    }    
}
