<?php
class Schuchert_DVR_SeasonPassManager
{
    private $_schedule;
    private $_toDoList = array();
 
    public function __construct($schedule)
    {
        $this->_schedule = $schedule;
    }

    public function sizeOfToDoList()
    {
        return count($this->_toDoList);
    }

    public function toDoListContentsFor($programId)
    {
        $result = array();
        foreach ($this->_toDoList as $current) {
            if ($current->getId() == $programId) {
                $result[] = $current;
            }
        }
        return $result;
    }

    public function createNewSeasonPass($programName, $channel)
    {
        $programsFound = $this->_schedule->findProgramsNamedOn(
            $programName, $channel
        );
        foreach ($programsFound as $current) {
            if (!$this->alreadyInToDoList($current)) {
                $this->_toDoList[] = $current;
            }
        }
        if (!empty($programsFound)) {
            return reset($programsFound);
        }
        return null;
    }
 
    private function alreadyInToDoList($candidate)
    {
        foreach ($this->_toDoList as $current) {
            if ($current->sameEpisodeAs($candidate)) {
                return true;
            }
        }
        return false;
    }
}