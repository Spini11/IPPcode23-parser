<?php
require_once('ErrorHandling.php');
class Syntaxis
{
    private static $state = "Check_first";

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
            default:
                ErrorHandling::ErrorSyntactical(3,$input, null, null);
        }
    }
    public static function Verify(&$input)
    {
        $i = 0;
        foreach ($input as $token)
        {
            switch (self::$state)
            {
                case "Check_first":
                    if($token[0] == 0) break;
                    if($token[0] == 16) ErrorHandling::ErrorSyntactical(3,$token, null, null);
                    if($token[0] > 8) ErrorHandling::ErrorSyntactical(4,$token, null, null);
                    self::$state = "Get_comm";
                case "Get_comm":
                    if($token[0] == 0) break;
                    if($token[0] == 16) ErrorHandling::ErrorSyntactical(3,$token, null, null);
                    self::GetCommandCat($token);
                    break;
                case "EOL":
                    if($token[0] != 0) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    self::$state = "Get_comm";
                    break;
                case "Var_op":
                    if($token[0] < 9 || $token[0] > 11) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    self::$state = "EOL";
                    break;
                case "Label_op":
                    if($token[0] != 16 && ($token[0] < 1 ||$token[0] > 8)) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    if($token[0] != 16) $input[$i][0] = 16;
                    self::$state = "EOL";
                    break;
                case "Symb_op":
                    if($token[0] < 9 || $token[0] > 15) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    self::$state = "EOL";
                    break;
                case "VS_op1":
                    if($token[0] < 9 || $token[0] > 11) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    self::$state = "Symb_op";
                    break;
                case "VT_op1":
                    if($token[0] < 9 || $token[0] > 11) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    self::$state = "VT_op2";
                    break;
                case "VT_op2":
                    if(!preg_match('/^(int|string|bool)$/',$token[1])) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    self::$state = "EOL";
                    $input[$i][0] = 17;
                    break;
                case "VSS_op1":
                    if($token[0] < 9 || $token[0] > 11) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    self::$state = "VSS_op2";
                    break;
                case "VSS_op2":
                    if($token[0] < 9 || $token[0] > 15) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    self::$state = "Symb_op";
                    break;
                case "LSS_op1":
                    if($token[0] != 16 && ($token[0] < 1 ||$token[0] > 8)) ErrorHandling::ErrorSyntactical(5,$token, $input, $i);
                    if($token[0] != 16) $input[$i][0] = 16;
                    self::$state = "VSS_op2";
                    break;
            }
            $i++;
        }
    }
}