<?php
class Examples_SlimTests_Bowling
{
    public function doTable($table)
    {
        $g = new Examples_SlimTests_Bowling_Game();
        $rollResults = array(
            "","","","","","","","","","","","","","","","","","","","",""
        );
        $scoreResults = array(
            "","","","","","","","","","","","","","","","","","","","",""
        );
        $this->rollBalls($table, $g);
        $this->evaluateScores($g, $table[1], $scoreResults);
        return array($rollResults, $scoreResults);
    }

    private function evaluateScores($g, $scoreRow, &$scoreResults)
    {
        for ($frame = 0; $frame < 10; $frame++) {
            $actualScore = $g->score($frame + 1);
            $expectedScore = (int) $scoreRow[$this->frameCoordinate($frame)];
            if ($expectedScore == $actualScore) {
                $result = "pass";
            } else {
                $result = sprintf(
                    "Was:%d, expected:%s.", $actualScore, $expectedScore
                );
            }
            $scoreResults[$this->frameCoordinate($frame)] = $result;
        }
    }

    private function frameCoordinate($frame)
    {
        return $frame < 9 ? $frame * 2 + 1 : $frame * 2 + 2;
    }

    private function rollBalls($table, $g)
    {
        $rollRow = $table[0];
        for ($frame = 0; $frame < 10; $frame++) {
            $firstRoll = $rollRow[$frame * 2];
            $secondRoll = $rollRow[$frame * 2 + 1];
            if (strtoupper($firstRoll) == "X") {
                $g->roll(10);
            } else {
                $firstRollInt = 0;
                if ($firstRoll == "-") {
                    $g->roll(0);
                } else {
                    $firstRollInt = (int) $firstRoll;
                    $g->roll($firstRollInt);
                }
                if ($secondRoll == "/") {
                    $g->roll(10 - $firstRollInt);
                } elseif ($secondRoll == "-") {
                    $g->roll(0);
                } else {
                    $g->roll((int) $secondRoll);
                }
            }
        }
    }
}
