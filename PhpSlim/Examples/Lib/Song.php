<?php
class Lib_Song
{
    private $_title;
    private $_artist;
    private $_duration;
    private $_credits;
    private $_rank;

    public function __construct($title, $artist, $duration, $credits, $rank)
    {
        $this->_title = $title;
        $this->_artist = $artist;
        $this->_duration = $duration;
        $this->_credits = $credits;
        $this->_rank = $rank;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function getArtist()
    {
        return $this->_artist;
    }

    public function getDuration()
    {
        return $this->_duration;
    }

    public function getCredits()
    {
        return $this->_credits;
    }

    public function getRank()
    {
        return $this->_rank;
    }
}
