<?php
class Schuchert_RemoveProgramById
{
    private $_id;

    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->setId($id);
            $this->execute();
        }
    }
    
    public function setId($id)
    {
        $this->_id = $id;
    }
   
    public function execute()
    {
        $schedule = Schuchert_AddProgramsToSchedule::getSchedule();
        $schedule->removeProgramById($this->_id);
    }
}
