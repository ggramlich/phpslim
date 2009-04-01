<?php
class Fixtures_JukeBox
{
    public static $jukeBox;
    private $_averageYouDecideCredits = 0;
    public static $youDecideCounts = array();

    public function __construct()
    {
        self::$jukeBox = new Lib_JukeBox();
    }

    public function credits()
    {
        return self::$jukeBox->credits();
    }

    public function deposit($payment)
    {
        self::$jukeBox->deposit($payment);
    }

    public function select($id)
    {
        self::$jukeBox->select($id);
    }

    public function nowPlaying()
    {
        $nowPlaying = self::$jukeBox->getNowPlaying();
        return $nowPlaying == null ? "none" : $nowPlaying->getTitle();
    }

    public function songFinished()
    {
        self::$jukeBox->songFinished();
    }

    public function repeatYouDecideTimesAndCountResults($times)
    {
        self::$youDecideCounts = array();
        $credits = 0;
        for ($i = 0; $i < $times; $i++) {
            $id = self::$jukeBox->youDecide();
            $song = self::$jukeBox->getSong($id);
            $credits += $song->getCredits();
            if (empty(self::$youDecideCounts[$id])) {
                self::$youDecideCounts[$id] = 0;
            }
            self::$youDecideCounts[$id]++;
        }
        $this->_averageYouDecideCredits = $credits / (double)$times;
    }

    public function averageYouDecideCredits()
    {
        return $this->_averageYouDecideCredits;
    }

    public function cashBalance()
    {
        $cashBalance = self::$jukeBox->cashBalance();
        return PhpSlim_TypeConverter::floatToString($cashBalance);
    }

    public function deposits()
    {
        $castCallback = array('PhpSlim_TypeConverter', 'floatToString');
        return array_map(
            $castCallback, self::$jukeBox->deposits()
        );
    }

    public function resetCash()
    {
        return self::$jukeBox->resetCash();
    }
}
