<?php
class Schuchert_ProgramProperties
{
    public $episodeName;
    public $date;
    public $startTime;
    private $_startDateTime;
    
    public function __construct($program)
    {
        $timeSlot = $program->timeSlot;
        $this->episodeName = $program->episodeName;
        $this->_startDateTime = $timeSlot->startDateTime;
        $this->date = $this->getStartDate();
        $this->startTime = $this->getStartTime();
    }
    
    public static function getProperties($program)
    {
        return new Schuchert_ProgramProperties($program);
    }

    public function getStartDate()
    {
        $day = $this->_startDateTime['tm_mday'];
        $month = 1 + $this->_startDateTime['tm_mon'];
        $year = 1900 + $this->_startDateTime['tm_year'];
        return sprintf('%d/%d/%d', $month, $day, $year);
    }

    public function getStartTime()
    {
        $minute = $this->_startDateTime['tm_min'];
        $hour = $this->_startDateTime['tm_hour'];
        return sprintf('%d:%02d', $hour, $minute);
    }
}
