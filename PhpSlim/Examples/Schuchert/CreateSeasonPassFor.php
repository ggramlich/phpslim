<?php
class Schuchert_CreateSeasonPassFor
{
    private static $_seasonPassManager;
    private $_lastProgramFound;
    
    public function __construct($programName, $channel)
    {
        $seasonPassManager = self::getSeasonPassManager();
        $this->_lastProgramFound = $seasonPassManager->createNewSeasonPass(
            $programName, $channel
        );
    }
 
    public function idOfProgramScheduled()
    {
        if (!is_null($this->_lastProgramFound)) {
            return $this->_lastProgramFound->getId();
        }
        return "n/a";
    }

    public static function getSeasonPassManager()
    {
        if (empty(self::$_seasonPassManager)) {
            $schedule = Schuchert_AddProgramsToSchedule::getSchedule();
            $seasonPassManager = new Schuchert_DVR_SeasonPassManager($schedule);
            self::$_seasonPassManager = $seasonPassManager;
        }
        return self::$_seasonPassManager;
    }
}
