<?php
class Fixtures_SongInventory
{
    private $_title;
    private $_artist;
    private $_duration;
    private $_credits;
    private $_rank;
    private $_lastId;

    public function setTitle($title)
    {
        $this->_title = $title;
    }

    public function setArtist($artist)
    {
        $this->_artist = $artist;
    }

    public function setDuration($duration)
    {
        $this->_duration = $duration;
    }

    public function setCredits($credits)
    {
        $this->_credits = $credits;
    }

    public function id()
    {
        return $this->_lastId;
    }

    public function execute()
    {
        $song = new Lib_Song(
            $this->_title, $this->_artist, $this->_duration,
            $this->_credits, $this->_rank
        );
        $this->_lastId = Fixtures_JukeBox::$jukeBox->addSong($song);
    }

    public function setRank($rank)
    {
        $this->_rank = $rank;
    }
}
