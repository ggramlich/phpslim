<?php
class Schuchert_ClearProgramSchedule
{
    public function __construct()
    {
        Schuchert_AddProgramsToSchedule::getSchedule()->clear();
    }
}
