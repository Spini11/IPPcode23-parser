<?php
class Generate
{
    private static function arg_proc($input)
    {
        if($input[0] >= 9 && $input[0] <= 11) return ["var", "$input[1]"];
        if($input[0] == 16) return ["label",$input[1]];
        if($input[0] == 17) return ["type",$input[1]];
        $symb = explode("@", $input[1]);
        if($input[0] >= 12 && $input[0] <= 15) return [$symb[0],$symb[1]];
    }
    public static function generate($input)
    {
        print("<program language=\"IPPcode23\">\n");
        $inst = 0;
        $arg = 0;
        foreach ($input as $token)
        {
            if($token[0] <= 8 && $token[0] != 0)
            {
                $inst++;
                $opcode = strtoupper($token[1]);
                print("\t<instruction order=\"$inst\" opcode=\"$opcode\">\n");
            }
            else if($token[0] == 0)
            {
                $arg = 0;
                print("\t</instruction>\n");
            }
            else
            {
                $arg++;
                $argprint = self::arg_proc($token);
                print("\t\t<arg$arg type=\"$argprint[0]\">$argprint[1]</arg$arg>\n");
            }
        }
        print("</program>\n");
    }
}
