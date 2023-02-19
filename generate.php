<?php
require_once('Array2XML.php');
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
    public static function generate3($input)
    {
        $array = ['@attributes' => [
            'language'=>'IPPcode23'
        ]];
        $inst = 0;
        $arg = 0;
        $tmp = null;
        foreach($input as $token)
        {
            if($token[0] <= 8 && $token[0] != 0)
            {
                $inst++;
                $opcode = strtoupper($token[1]);
                $tmp = ['instruction' => [
                    '@attributes'=> [
                        'order' => $inst,
                        'opcode' => $token[1]
                    ],
                ]];
            }
            else if($token[0] == 0)
            {
                $arg = 0;
                print_r($array);
                $array = array_merge($array, $tmp);
                $tmp = null;
            }
            else
            {
                $arg++;
                $argprint = self::arg_proc($token);
                $tmp['instruction']=array_merge($tmp['instruction'],["arg$arg"=>[
                    '@value' => $argprint[1],
                    '@attributes' => [
                        'type' => $argprint[0],
                    ]
                ]]);
            }
        }
        $xml = Array2XML::createXML('program', $array);
        echo $xml->saveXML();
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
