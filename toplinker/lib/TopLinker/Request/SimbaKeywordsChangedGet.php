<?php

class TopLinker_Request_SimbaKeywordsChangedGet extends TopLinker_Request_Abstract
{
    public $nick;

    public $start_time;

    public $page_size;

    public $page_no;

    public function rules()
    {
        return array();
    }
}