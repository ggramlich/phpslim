<?php
class Schuchert_AddProgramsToSchedule
{
    private static $_dateFormat = "M/d/yyyy|h:mm";
    private static $_schedule;
    private $_channel;
    private $_date;
    private $_startTime;
    private $_minutes;
    private $_programName;
    private $_episodeName;
    private $_lastId;
    private $_lastCreationSuccessful;

    public static function getSchedule()
    {
        if (empty(self::$_schedule)) {
            self::$_schedule = new Schuchert_DVR_Schedule();
        }
        return self::$_schedule;
    }

    public function setName($name)
    {
        $this->_programName = $name;
    }
 
    public function setEpisode($name)
    {
        $this->_episodeName = $name;
    }
 
    public function setChannel($channel)
    {
        $this->_channel = $channel;
    }
 
    public function setDate($date)
    {
        $this->_date = $date;
    }
 
    public function setStartTime($startTime)
    {
        $this->_startTime = $startTime;
    }
 
    public function setMinutes($minutes)
    {
        $this->_minutes = $minutes;
    }
   
    public function execute()
    {
        try {
            $p = self::getSchedule()->addProgram(
                $this->_programName, $this->_episodeName, $this->_channel,
                $this->buildStartDateTime(), $this->_minutes
            );
            $this->_lastId = $p->getId();
            $this->_lastCreationSuccessful = true;
        } catch (Schuchert_DVR_ConflictingProgramException $e) {
            $this->_lastCreationSuccessful = false;
        }
    }

    public function created()
    {
        return $this->_lastCreationSuccessful;
    }

    public function lastId()
    {
        if ($this->_lastCreationSuccessful) {
            return $this->_lastId;
        }
        return 'n/a';
    }

    private function buildStartDateTime()
    {
        $dateTime = sprintf("%s %s", $this->_date, $this->_startTime);
        $dateArray = strptime($dateTime, '%m/%d/%Y %H:%M');
        if (false === $dateArray || !empty($dateArray['unparsed'])) {
            throw new RuntimeException("Unable to parse date/time");
        }
        return $dateArray;
    }
}
