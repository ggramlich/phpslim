<?php
class Fixtures_PlayList
{
    public function query()
    {
        $table = array();
        $songs = Fixtures_JukeBox::$jukeBox->getPlayList();
        foreach ($songs as $i => $id) {
            $songHash = array(
                'index' => $i+1,
                'id' => $id,
                'title' => Fixtures_JukeBox::$jukeBox->getTitleOf($id),
            );
            $table[] = PhpSlim_TypeConverter::hashToPairs($songHash);
        }
        return $table;
    }
}
