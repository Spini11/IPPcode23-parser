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

    private static function GetWords($input)
    {

        if(!preg_match('/(\s)+/',$input)) return [$input];
        $i = 0;
        $output = [];
        $j = 0;
        $tmp = "";
        while(!preg_match('/^(\n|\z)$/',$input[$i]))
        {
            if(!preg_match('/^\s$/', $input[$i]) && $input[$i] != "#")
            {
                $tmp .= $input[$i];
            }
            else if(!preg_match('/^\s$/', $input[$i+1]))
            {
                $output[$j] = $tmp;
                $j++;
                $tmp = "";
            }
            else if ($input[$i] == "#")
            {
                $output[$j] = $tmp;
                return $output;
            }
            $i++;
        }
        $output[$j] = $tmp;
        return $output;
    }

    private static function GetLexeme($input)
    {
        if(self::IsCommentWhite($input)) return 0;
        if(preg_match('/^(?i:((CREATEFRAME)|(PUSHFRAME)|(POPFRAME)|(RETURN)|(BREAK)))$/',$input)) return 1;
        if(preg_match('/^(?i:((DEFVAR|CALL|PUSHS|POPS|WRITE|LABEL|JUMP|EXIT|DPRINT)))$/', $input)) return 2;
        if(preg_match('/^(?i:((MOVE|INT2CHAR|READ|TYPE)))$/', $input)) return 3;
        if(preg_match('/^(?i:((ADD|SUB|MUL|IDIV|LT|GT|EQ|AND|OR|NOT|STRI2INT|CONCAT|GETCHAR|SETCHAR|JUMPIFEQ)))$/', $input)) return 4;
        if(preg_match('/^GF@[A-Z,a-z,0-9,_,\-,$,&,%,*,!,?]*$/', $input)) return 5;
        if(preg_match('/^LF@[A-Z,a-z,0-9,_,\-,$,&,%,*,!,?]*$/', $input)) return 6;
        if(preg_match('/^TF@[A-Z,a-z,0-9,_,\-,$,&,%,*,!,?]*$/', $input)) return 7;
        if(preg_match('/^bool@(true|false)$/', $input)) return 8;
        if($input == 'nil@nil') return 9;
        if(preg_match('/^int@[A-Z,a-z,0-9,\-,+]+$/', $input)) return 10;
        if(preg_match('/^string@([A-Z,a-z,0-9,\pL](\\\\\pN{3})*)*$/u', $input)) return 11;
        if(preg_match('/^[a-z,A-Z,0-9,_,\-,$,&,%,*,!,?,\pL]*$/u', $input)) return 12;
        return -1;
    }

    public static function Tokenize($input, &$tokens)
    {
        if(self::IsCommentWhite($input))
        {
            return;
        }
        $words = self::GetWords($input);
        foreach($words as $word)
        {
            $Lexem = self::GetLexeme($word);
            if($Lexem == -1)
            {
                if(preg_match('/(@|\\\)/',$input)) exit(23);
                exit(22);
            }
            else if($Lexem == 0)
            {
                break;
            }
            $tokens[] = [$Lexem, $word];
        }

    }
}