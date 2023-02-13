<?php

class LexToken
{
    public $type;
    public $name;

    function GetLexeme($input)
    {

    }
    function IsComment($input)
    {
        return preg_match('/^#/',$input);
    }
}