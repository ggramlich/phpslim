<?php
require_once 'PHPUnit/Framework.php';
class Schuchert_Tests_SeasonPassManagerTest extends PHPUnit_Framework_TestCase
{
    private $_seasonPassManager;
    private $_schedule;
    
    private function createDate($year, $month, $day, $hour, $min)
    {
        return array(
            'tm_sec' => 0,
            'tm_min' => $min,
            'tm_hour' => $hour,
            'tm_mday' => $day,
            'tm_mon' => $month - 1,
            'tm_year' => $year - 1900,
            'tm_wday' => 0,
            'tm_yday' => 0,
            'unparsed' => null
        );
    }

    public function setup()
    {
        $schedule = new Schuchert_DVR_Schedule();
        $this->_schedule = $schedule;
        $schedule->addProgram(
            "p1", "e1", 7, $this->createDate(2008, 4, 5, 7, 0), 60
        );
        $schedule->addProgram(
            "p2", "e2", 7, $this->createDate(2008, 4, 5, 8, 0), 60
        );
        $seasonPassManager = new Schuchert_DVR_SeasonPassManager($schedule);
        $this->_seasonPassManager = $seasonPassManager;
    }

    public function testNewSeasonPassManagerHasEmptyToDoList()
    {
        $this->assertEquals(0, $this->_seasonPassManager->sizeOfToDoList());
    }

    public function testScheduleProgramWithOneEpisodeToDoListIs1()
    {
        $this->_seasonPassManager->createNewSeasonPass("p1", 7);
        $this->assertEquals(1, $this->_seasonPassManager->sizeOfToDoList());
    }
}
