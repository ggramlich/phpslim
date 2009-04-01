<?php
class Examples_SlimTests_Bowling_Game
{
    private $_rolls = array();
    private $_currentRoll = 0;

    public function roll($pins)
    {
        $this->_rolls[$this->_currentRoll++] = $pins;
    }

    public function score($frame)
    {
        $score = 0;
        $firstBall = 0;
        for ($f = 0; $f < $frame; $f++) {
            if ($this->isStrike($firstBall)) {
                $score += 10 + $this->nextTwoBallsForStrike($firstBall);
                $firstBall += 1;
            } elseif ($this->isSpare($firstBall)) {
                $score += 10 + $this->nextBallForSpare($firstBall);
                $firstBall += 2;
            } else {
                $score += $this->twoBallsInFrame($firstBall);
                $firstBall += 2;
            }
        }
        return $score;
    }

    private function twoBallsInFrame($firstBall)
    {
        return $this->_rolls[$firstBall] + $this->_rolls[$firstBall + 1];
    }

    private function nextBallForSpare($firstBall)
    {
        if (empty($this->_rolls[$firstBall + 2])) {
            return null;
        }
        return $this->_rolls[$firstBall + 2];
    }

    private function nextTwoBallsForStrike($firstBall)
    {
        return $this->_rolls[$firstBall + 1] + $this->_rolls[$firstBall + 2];
    }

    private function isSpare($firstBall)
    {
        $bothRolls = $this->_rolls[$firstBall] + $this->_rolls[$firstBall + 1];
        return 10 == $bothRolls;
    }

    private function isStrike($firstBall)
    {
        return 10 == $this->_rolls[$firstBall];
    }
}
