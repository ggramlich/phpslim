<?php
class Fixtures_YouDecideResults
{
    private $_id;

    public function setId($id)
    {
        $this->_id = $id;
    }

    public function timesPlayed()
    {
        if (empty(Fixtures_JukeBox::$youDecideCounts[$this->_id])) {
            return 0;
        }
        return Fixtures_JukeBox::$youDecideCounts[$this->_id];
    }
}
