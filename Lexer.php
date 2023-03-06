<?php
require_once('ErrorHandling.php');
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
        $output = [];
        $j = 0;
        $tmp = "";

        $chars = str_split($input);
        foreach($chars as $i=>$char)
        {
            if(!preg_match('/^\s$/', $input[$i]) && $input[$i] != "#")
            {
                $tmp .= $input[$i];
            }
            else if(preg_match('/^\s$/', $input[$i]) && !preg_match('/^\s$/', $input[$i-1]))
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
        }
        $output[$j] = $tmp;
        return $output;
    }

    private static function GetLexeme($input,$lineN)
    {
        if(self::IsCommentWhite($input)) return 0;
        if(preg_match('/^(?i:((CREATEFRAME)|(PUSHFRAME)|(POPFRAME)|(RETURN)|(BREAK)))$/',$input)) return 1;
        if(preg_match('/^(?i:(DEFVAR|POP|POPS))$/',$input)) return 2;
        if(preg_match('/^(?i:(LABEL|JUMP|CALL))$/',$input)) return 3;
        if(preg_match('/^(?i:(PUSHS|WRITE|EXIT|DPRINT))$/',$input)) return 4;
        if(preg_match('/^(?i:(MOVE|NOT|INT2CHAR|STRLEN|TYPE))$/',$input)) return 5;
        if(preg_match('/^(?i:(READ))$/',$input)) return 6;
        if(preg_match('/^(?i:(ADD|SUB|MUL|IDIV|LT|GT|EQ|AND|OR|STRI2INT|CONCAT|GETCHAR|SETCHAR))$/',$input)) return 7;
        if(preg_match('/^(?i:(JUMPIFEQ|JUMPIFNEQ))$/',$input)) return 8;
        if(preg_match('/^GF@[A-Za-z_\-$&%*!?\pL]+[A-Za-z0-9_\-$&%*!?\pL]*$/u', $input)) return 9;
        if(preg_match('/^LF@[A-Za-z_\-$&%*!?\pL]+[A-Za-z0-9_\-$&%*!?\pL]*$/u', $input)) return 10;
        if(preg_match('/^TF@[A-Za-z_\-$&%*!?\pL]+[A-Za-z0-9_\-$&%*!?\pL]*$/u', $input)) return 11;
        if(preg_match('/^bool@(true|false)$/', $input)) return 12;
        if($input == 'nil@nil') return 13;
        if(preg_match('/^int@(([\x2B\\x2D]?(([1-9][0-9]*(_[0-9]*)*\d*)|(0))$)|([\x2B\\x2D]?(0(x|X)([A-Fa-f0-9]+(_[A-Fa-f0-9]+)*)+)$)|([\x2B\\x2D]?(0((o|O)([0-7][0-7_]*[0-7]+)|([0-7_]*[0-7]+)))$))$/', $input)) return 14;
        if(preg_match('/^string@([^#\s\x0-\x1A\\\]*(\\\\\pN{3,})*)*\s*$/u',$input)) return 15;
        if(preg_match('/^[a-zA-Z0-9_\-$&%*!?\pL]*$/u', $input)) return 16;

        if(preg_match('/[@\/\\\]/',$input)) ErrorHandling::ErrorLexical(2,$lineN,$input);
        ErrorHandling::ErrorLexical(1,$lineN,$input);
    }

    public static function Tokenize($input, &$tokens, $lineN)
    {
        if(self::IsCommentWhite($input))
        {
            return;
        }
        $words = self::GetWords($input);
        foreach($words as $word)
        {
            $Lexem = self::GetLexeme($word, $lineN);
            if($Lexem == 0)
            {
                break;
            }
            $tokens[] = [$Lexem, $word, $lineN];
        }
    }
}