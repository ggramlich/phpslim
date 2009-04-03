<?php
class Schuchert_EpisodesInToDoList
{
    private $_programId;
    
    public function __construct($programId)
    {
        $this->_programId = $programId;
    }

    public function query()
    {
        $manager = Schuchert_CreateSeasonPassFor::getSeasonPassManager();
        $programs = $manager->toDoListContentsFor($this->_programId);
        $callback = array('Schuchert_ProgramProperties', 'getProperties');
        $properties = array_map($callback, $programs);
        return PhpSlim_TypeConverter::objectListToPairsList($properties);
    }
}
