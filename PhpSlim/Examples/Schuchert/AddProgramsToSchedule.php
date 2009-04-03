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

    public function __construct()
    {
        if (empty(self::$_schedule)) {
            self::$_schedule = new Schuchert_DVR_Schedule();
        }
    }

    public static function getSchedule()
    {
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
   
    public function created()
    {
        try {
            $p = self::$_schedule->addProgram(
                $this->_programName, $this->_episodeName, $this->_channel,
                $this->buildStartDateTime(), $this->_minutes
            );
            $this->_lastId = $p->getId();
        } catch (Schuchert_DVR_ConflictingProgramException $e) {
            $this->_lastId = 'n/a';
            return false;
        }
        return true;
    }

    public function lastId()
    {
        return $this->_lastId;
    }

    private function buildStartDateTime()
    {
        $dateTime = sprintf("%s %s", $this->_date, $this->_startTime);
        $dateArray = strptime($dateTime, '%l/%e/%Y %H:%M:%S');
        if (!empty($dateArray['unparsed'])) {
            throw new RuntimeException("Unable to prase date/time", e);
        }
        return $dateArray;
    }
}
