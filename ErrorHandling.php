<?php
class ErrorHandling {

    private static function NumToName($input):string
    {
        //Translates category number into operand type
        switch($input)
        {
            case 9: case 10: case 11:
                return "variable";
            case 12: case 13: case 14: case 15:
                return "constant";
            case 16:
                return "label";
        }
        return "label";
    }
    public static function ErrorHeader($line): void
    {
        fwrite(STDERR,"Incorrect header on line $line. Header must be .IPPcode23.\n");
        exit(21);
    }
    public static function ErrorLexical($errcode, $line, $token): void
    {
        /*
         * Internal Lexical Error codes
         * 1 = Token doesn't comply with lexical rules
         * 2 = Invalid variable or Constant
         */
        if ($errcode == 1) {
            fwrite(STDERR, "Invalid token $token on line $line.\n");
            exit(22);
        } else if ($errcode == 2) {
            if (preg_match('/^(?i:(GF@|LF@|TF@))/', $token)) {
                fwrite(STDERR, "Invalid format of a variable $token on line $line.\n");
                if (preg_match('/^(gf|Gf|gF)@/', $token)) fwrite(STDERR, "\tFix this error by by writing GF@ in capital letters in $token.\n");
                else if (preg_match('/^(lf|Lf|lF)@/', $token)) fwrite(STDERR, "\tFix this error by writing LF@ in capital letters in $token.\n");
                else if (preg_match('/^(tf|Tf|tF)@/', $token)) fwrite(STDERR, "\tFix this error by writing TF@ in capital letters in $token.\n");
                else if (!preg_match('/F@[A-Za-z_\-$&%*!?\pL]+/', $token))
                    fwrite(STDERR, "\tFix this error by using letter or one of the allowed symbols as first character of the variable name.\n");
                else if (preg_match('/F@[A-Za-z_\-$&%*!?\pL]+/', $token))
                    fwrite(STDERR, "\tFix this error by using only letters, numbers or allowed symbols in the variable name.\n");
            } else {
                fwrite(STDERR, "Invalid format of a constant $token on line $line.\n");
                if (preg_match('/^(?i:(int@))/', $token)) {
                    if (!str_starts_with($token, "int@"))
                        fwrite(STDERR, "\tFix this error by writing int@ in lowercase letters in $token.\n");
                    else fwrite(STDERR, "\tFix this error by using valid decimal, hexadecimal or octal representation of int value.\n");
                } else if (preg_match('/^(?i:(string@))/', $token)) {
                    if (!str_starts_with($token, "string@"))
                        fwrite(STDERR, "\tFix this error by writing string@ in lowercase letters in $token.\n");
                    else fwrite(STDERR, "\tFix this error by using valid string representation.\n");
                } else if (preg_match('/^(?i:(bool@))/', $token)) {
                    if (!str_starts_with($token, "bool@"))
                        fwrite(STDERR, "\tFix this error by writing bool@ in lowercase letters in $token.\n");
                    else fwrite(STDERR, "\tFix this error by using only \"true\" or \"false\" as value.\n");
                } else if (preg_match('/^(?i:(nil@nil))/', $token)) fwrite(STDERR, "\tFix this error by writing nil@nil in lowercase letters in $token.\n");
                else fwrite(STDERR, "\tFix this error by using int@, string@, bool@ or nil@nil.");
            }
            exit(23);
        }
    }
    public static function ErrorSyntactical($errcode, $token, $input, $i)
    {
        /*
         * Internal Syntactical Error codes
         * 3 = Invalid opcode
         * 4 = Invalid token as opcode
         * 5 = Invalid operands
         */
        if($errcode == 3)
        {
            if(preg_match('/[_\-$&%*!?]/',$token[1])) $errcode = 4;
            else
            {
                fwrite(STDERR, "Invalid opcode $token[1] on line $token[2].\n");
                exit(22);
            }
        }
        if($errcode == 4)
        {
            fwrite(STDERR, "Opcode needs to be first instead of $token[1] on line $token[2].\n");
            exit(23);
        }
        else if($errcode == 5)
        {
            //$i is token position in file
            $j = $i-1;
            $k = 0;
            //Gets to first token of the line
            while($input[$j][0] <1 || $input[$j][0] >8)
            {
                $j--;
            }
            $command = $input[$j][1];
            $commandCat = $input[$j][0];
            $operands = null;
            //creates array of operands
            while($input[$j+1][0] != 0)
            {
                $operands[$k][1] = $input[$j+1][1];
                $operands[$k][0] = $input[$j+1][0];
                $k++;
                $j++;
            }

            //Errors when no operands are present
            if ($operands == null)
            {
                if($commandCat == 2)
                    fwrite(STDERR, "Command $command on the line {$input[$i-1][2]} was supplied with no operands, but expects variable as operand.\n");
                else if($commandCat == 3)
                    fwrite(STDERR, "Command $command on the line {$input[$i-1][2]} was supplied with no operands, but expects label as operand.\n");
                else if($commandCat == 4)
                    fwrite(STDERR, "Command $command on the line {$input[$i-1][2]} was supplied with no operands, but expects symbol as operand.\n");
                else if($commandCat == 5)
                    fwrite(STDERR, "Command $command on the line {$input[$i-1][2]} was supplied with no operands, but expects variable and a symbol as operand.\n");
                else if($commandCat == 6)
                    fwrite(STDERR, "Command $command on the line {$input[$i-1][2]} was supplied with no operands, but expects variable and a type as operand.\n");
                else if($commandCat == 7)
                    fwrite(STDERR, "Command $command on the line {$input[$i-1][2]} was supplied with no operands, but expects variable, symbol and symbol as operand.\n");
                else if($commandCat == 8)
                    fwrite(STDERR, "Command $command on the line {$input[$i-1][2]} was supplied with no operands, but expects label, symbol and symbol as operand.\n");
                exit(23);
            }
            if($commandCat == 1)
            {
                fwrite(STDERR, "Command $command on line {$input[$i][2]} expects no operand, but was supplied with \"{$operands[0][1]}\".\n");
            }
            foreach ($operands as $index=>$operand)
            {
                //Each command category expects different operand types, this is used for accurate error messages.
                $type = self::NumToName($operand[0]);
                $ArgNum = $index+1;
                if($commandCat == 2)
                {
                    if($index == 0)
                    {
                        if($type != "variable")
                            fwrite(STDERR, "Operand 1 to command $command on line {$input[$i][2]} is expected to be a variable, but was supplied with \"{$operands[0][1]}\" which is a $type.\n");
                        continue;
                    }
                    fwrite(STDERR, "Operand $ArgNum to command $command on line {$input[$i][2]} was unexpected. Command $command expects only variable as operand.\n");
                }
                else if ($commandCat == 3)
                {
                        if($index == 0)
                        {
                            if($type != "label")
                                fwrite(STDERR, "Operand 1 to command $command on line {$input[$i][2]} is expected to be a label, but was supplied with \"{$operands[0][1]}\" which is a $type.\n");
                            continue;
                        }
                        fwrite(STDERR, "Operand $ArgNum to command $command on line {$input[$i][2]} was unexpected. Command $command expects only label as operand.\n");
                }
                else if ($commandCat == 4)
                {
                    if($index == 0)
                    {
                        if($type != "variable" && $type != "constant")
                            fwrite(STDERR, "Operand 1 to command $command on line {$input[$i][2]} is expected to be a symbol, but was supplied with \"{$operands[0][1]}\" which is a $type.\n");
                        continue;
                    }
                    fwrite(STDERR, "Operand $ArgNum to command $command on line {$input[$i][2]} was unexpected. Command $command expects only symbol as operand.\n");
                }
                else if ($commandCat == 5)
                {
                    if($index == 0)
                    {
                        if($type != "variable")
                            fwrite(STDERR, "Operand 1 to command $command on line {$input[$i][2]} is expected to be a variable, but was supplied with \"{$operands[0][1]}\" which is a $type.\n");
                        if($k < 2)
                            fwrite(STDERR, "Operand 2 to command $command on line {$input[$i-1][2]} is expected to be a variable, but operand is missing.\n");
                        continue;
                    }
                    if($index == 1)
                    {
                        if($type != "variable" && $type != "constant")
                            fwrite(STDERR, "Operand 2 to command $command on line {$input[$i][2]} is expected to be a symbol, but was supplied with \"{$operands[1][1]}\" which is a $type.\n");
                        continue;
                    }
                    fwrite(STDERR, "Operand $ArgNum to command $command on line {$input[$i][2]} was unexpected. Command $command expects only variable and a symbol as operand.\n");
                }
                else if ($commandCat == 6)
                {
                    if($index == 0)
                    {
                        if($type != "variable")
                            fwrite(STDERR, "Operand 1 to command $command on line {$input[$i][2]} is expected to be a variable, but was supplied with \"{$operands[0][1]}\" which is a $type.\n");
                        if($k < 2)
                            fwrite(STDERR, "Operand 2 to command $command on line {$input[$i-1][2]} is expected to be a type, but operand is missing.\n");
                        continue;
                    }
                    if($index == 1)
                    {
                        if(!preg_match('/^(int|string|bool)$/', $operands[1][1]))
                            fwrite(STDERR, "Operand 2 to command $command on line {$input[$i][2]} is expected to be a type, but was supplied with \"{$operands[1][1]}\" which is a $type.\n");
                        continue;
                    }
                    fwrite(STDERR, "Operand $ArgNum to command $command on line {$input[$i][2]} was unexpected. Command $command expects only variable and a type as operand.\n");
                }
                else if ($commandCat == 7)
                {
                    if($index == 0)
                    {
                        if($type != "variable")
                            fwrite(STDERR, "Operand 1 to command $command on line {$input[$i][2]} is expected to be a variable, but was supplied with \"{$operands[0][1]}\" which is a $type.\n");
                        if($k < 2)
                            fwrite(STDERR, "Operand 2 to command $command on line {$input[$i-1][2]} is expected to be a symbol, but operand is missing.\nOperand 3 to command $command on line {$input[$i-1][2]} is expected to be a symbol, but operand is missing.\n");
                        continue;
                    }
                    if($index == 1)
                    {
                        if($type != "variable" && $type != "constant")
                            fwrite(STDERR, "Operand 2 to command $command on line {$input[$i][2]} is expected to be a symbol, but was supplied with \"{$operands[1][1]}\" which is a $type.\n");
                        if($k < 3)
                            fwrite(STDERR, "Operand 3 to command $command on line {$input[$i-1][2]} is expected to be a symbol, but operand is missing.\n");
                        continue;
                    }
                    if($index == 2)
                    {
                        if($type != "variable" && $type != "constant")
                            fwrite(STDERR, "Operand 3 to command $command on line {$input[$i][2]} is expected to be a symbol, but was supplied with \"{$operands[2][1]}\" which is a $type.\n");
                        continue;
                    }
                    fwrite(STDERR, "Operand $ArgNum to command $command on line {$input[$i][2]} was unexpected. Command $command expects only variable, symbol and symbol as operand.\n");
                }
                else if ($commandCat == 8)
                {
                    if($index == 0)
                    {
                        if($type != "label")
                            fwrite(STDERR, "Operand 1 to command $command on line {$input[$i][2]} is expected to be a label, but was supplied with \"{$operands[0][1]}\" which is a $type.\n");
                        if($k < 2)
                            fwrite(STDERR, "Operand 2 to command $command on line {$input[$i-1][2]} is expected to be a symbol, but operand is missing.\nOperand 3 to command $command on line {$input[$i-1][2]} is expected to be a symbol, but operand is missing.\n");
                        continue;
                    }
                    if($index == 1)
                    {
                        if($type != "variable" && $type != "constant")
                            fwrite(STDERR, "Operand 2 to command $command on line {$input[$i][2]} is expected to be a symbol, but was supplied with \"{$operands[1][1]}\" which is a $type.\n");
                        if($k < 3)
                            fwrite(STDERR, "Operand 3 to command $command on line {$input[$i-1][2]} is expected to be a symbol, but operand is missing.\n");
                        continue;
                    }
                    if($index == 2)
                    {
                        if($type != "variable" && $type != "constant")
                            fwrite(STDERR, "Operand 3 to command $command on line {$input[$i][2]} is expected to be a symbol, but was supplied with \"{$operands[2][1]}\" which is a $type.\n");
                        continue;
                    }
                    fwrite(STDERR, "Operand $ArgNum to command $command on line {$input[$i][2]} was unexpected. Command $command expects only label, symbol and symbol as operand.\n");
                }
            }
            exit(23);
        }
    }
}