<?php
class Schuchert_DVR_Schedule
{
    private $_scheduledPrograms = array();
 
    public function addProgram($programName, $episodeName, $channel,
        $startDateTime, $lengthInMinutes)
    {
        $timeSlot = new Schuchert_DVR_TimeSlot(
            $channel, $startDateTime, $lengthInMinutes
        );
        if ($this->conflictsWithOtherTimeSlots($timeSlot)) {
            throw new Schuchert_DVR_ConflictingProgramException();
        }
 
        $program = new Schuchert_DVR_Program(
            $programName, $episodeName, $timeSlot
        );
        $this->_scheduledPrograms[] = $program;
        return $program;
    }

    public function removeProgramById($programIdToRemove)
    {
        foreach ($this->_scheduledPrograms as $key => $program) {
            if ($program->getId() == $programIdToRemove) {
                unset($this->_scheduledPrograms[$key]);
                break;
            }
        }
    }

    private function conflictsWithOtherTimeSlots($timeSlot)
    {
        foreach ($this->_scheduledPrograms as $current) {
            if ($current->timeSlot->conflictsWith($timeSlot)) {
                return true;
            }
        } 
        return false;
    }
}
