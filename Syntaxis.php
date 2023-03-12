<?php
require_once('ErrorHandling.php');
class Syntaxis
{
    private static $state = "Get_comm";

    private static function GetCommandCat($input)
    {
        switch ($input[0])
        {
            case 1:
                self::$state = "EOL";
                break;
            case 2:
                self::$state = "Var_op";
                break;
            case 3:
                self::$state = "Label_op";
                break;
            case 4:
                self::$state = "Symb_op";
                break;
            case 5:
                self::$state = "VS_op1";
                break;
            case 6:
                self::$state = "VT_op1";
                break;
            case 7:
                self::$state = "VSS_op1";
                break;
            case 8:
                self::$state = "LSS_op1";
                break;
            case 16:
                //label present instead of opcode means command doesn't exist
                ErrorHandling::ErrorSyntactical(3,$input, null, null);
            default:
                //Op code needs to be first token on a line
                ErrorHandling::ErrorSyntactical(3,$input, null, null);
        }
    }
    public static function Verify(&$input)
    {
        foreach ($input as $i=>$token)
        {
            switch (self::$state)
            {
                case "Get_comm": //initial state
                    if($token[0] == 0) break;
                    self::GetCommandCat($token); //Function changes the state based on the command category, exits if not a command.
                    break;
                case "EOL":
                    //verifies end of line is actually present
                    if($token[0] != 0) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    self::$state = "Get_comm";
                    break;
                case "Var_op": //op code expecting only variable
                    if($token[0] < 9 || $token[0] > 11) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    self::$state = "EOL"; //transition into end of line state
                    break;
                case "Label_op": //op code expecting only label
                    if($token[0] != 16 && ($token[0] < 1 ||$token[0] > 8)) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    if($token[0] != 16) $input[$i][0] = 16;
                    self::$state = "EOL";
                    break;
                case "Symb_op": //op code expecting only symbol
                    if($token[0] < 9 || $token[0] > 15) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    self::$state = "EOL";
                    break;
                case "VS_op1": //op code expecting variable and symbol, variable check
                    if($token[0] < 9 || $token[0] > 11) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    self::$state = "Symb_op";
                    break;
                case "VT_op1": //op code expecting variable and type, variable check
                    if($token[0] < 9 || $token[0] > 11) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    self::$state = "VT_op2";
                    break;
                case "VT_op2": //op code expecting variable and type, type check
                    if(!preg_match('/^(int|string|bool)$/',$token[1])) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    self::$state = "EOL";
                    $input[$i][0] = 17;
                    break;
                case "VSS_op1": //op code expecting variable and two symbols, variable check
                    if($token[0] < 9 || $token[0] > 11) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    self::$state = "VSS_op2";
                    break;
                case "VSS_op2": //op code expecting variable and two symbols, first symbol check
                    if($token[0] < 9 || $token[0] > 15) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    self::$state = "Symb_op";
                    break;
                case "LSS_op1": //op code expecting label and two symbols, label check
                    if($token[0] != 16 && ($token[0] < 1 ||$token[0] > 8)) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    if($token[0] != 16) $input[$i][0] = 16;
                    self::$state = "VSS_op2";
                    break;
            }
        }
    }
}