<?php

class Lexems
{

    public static function IsCommentWhite($input)
    {
        if(preg_match('/^\s*$/',$input) || preg_match('/^\s*#/', $input))
        {
            return 1;
        }
        return 0;
    }

    private static function GetLexeme($input)
    {
        if(preg_match('/^\s*((CREATEFRAME)|(PUSHFRAME)|(POPFRAME)|(RETURN)|(BREAK))\s*$/',$input)) return 1;
        if(preg_match('/^\s*(DEFWAR|CALL|PUSHS|POPS|WRITE|LABEL|JUMP|EXIT|DPRINT)\s*$/', $input)) return 2;
        if(preg_match('/^\s*(MOVE|INT2CHAR|READ|TYPE)\s*$/', $input)) return 3;
        if(preg_match('/^\s*(ADD|SUB|MUL|IDIV|LT|GT|EQ|AND|OR|NOT|STRI2INT|CONCAT|GETCHAR|SETCHAR|JUMPIFEQ)\s*$/')) return 4;

    }

    public static function Tokenize($input, &$tokens)
    {
        if(self::IsCommentWhite($input))
        {
            return;
        }

        self::GetLexeme($input);
    }
}