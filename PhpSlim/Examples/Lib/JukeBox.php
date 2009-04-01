<?php
class Lib_JukeBox
{
    private $_credits = 0;
    private $_rankTotal = 0;
    private $_cashBalance = 0.0;
    private $_deposits = array();
    private $_songInventory = array();
    private $_playList;
    private $_lastId = 1;

    public static function creditsFor($payment)
    {
        if ($payment == 0.25) {
            return 1;
        } elseif ($payment == 1.00) {
            return 5;
        } elseif ($payment == 5.00) {
            return 25;
        } elseif ($payment == 10.00) {
            return 60;
        } else {
            return -1;
        }
    }

    public function credits()
    {
        return $this->_credits;
    }

    public function deposit($payment)
    {
        $this->_credits += self::creditsFor($payment);
        $this->_cashBalance += $payment;
        $this->_deposits[] = $payment;
    }

    public function select($id)
    {
        $song = $this->_songInventory[$id];
        $this->_credits -= $song->getCredits();
        $this->_playList[] = $id;
    }

    public function addSong(Lib_Song $song)
    {
        $id = "A" . $this->_lastId++;
        $this->_songInventory[$id] = $song;
        $this->_rankTotal += $song->getRank();
        return $id;
    }

    public function getNowPlaying()
    {
        if (empty($this->_playList)) {
            return null;
        }
        return $this->_songInventory[$this->_playList[0]];
    }

    public function songFinished()
    {
        array_shift($this->_playList);
    }

    public function getPlayList()
    {
        return $this->_playList;
    }

    public function getTitleOf($id)
    {
        return $this->_songInventory[$id]->getTitle();
    }

    public function youDecide()
    {
        $selector = rand(0, $this->_rankTotal - 1);
        $ids = array_keys($this->_songInventory);
        foreach ($ids as $id) {
            $song = $this->_songInventory[$id];
            $selector -= $song->getRank();
            if ($selector < 0) {
                return $id;
            }
        }
        return "TILT";
    }

    public function getSong($id)
    {
        return $this->_songInventory[$id];
    }

    public function cashBalance()
    {
        return $this->_cashBalance;
    }

    public function deposits()
    {
        return $this->_deposits;
    }

    public function resetCash()
    {
        $this->_cashBalance = 0.0;
        $this->_deposits = array();
    }
}
