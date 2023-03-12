<?php
    require_once('Lexer.php');
    require_once('Syntaxis.php');
    require_once('generate.php');
    require_once('ErrorHandling.php');
    if($argc == 2 && $argv[1] == "--help")
    {
        print("parse.php expects input on STDIN after being launched without any argument.");
        exit(0);
    }
    else if ($argc >= 2)
    {
        print("Invalid launch arguments, use --help.");
        exit(10);
    }
    $f = fopen( 'php://stdin', 'r' );
    $lineN = 0;
    //Header validity check
    while(!preg_match('/^\s*(?i:(.IPPcode23))(\s*$|#|\s*#)/', ($line = fgets($f))))
    {
        if(str_contains($line,"\n")) $lineN++; //Tracks current line number
        if(!Lexems::IsCommentWhite($line))
            ErrorHandling::ErrorHeader($lineN); //Error if line isn't empty or doesn't contain header or comment
    }
    $lineN++;
    $tokens = [];
    while( $line = fgets( $f ) ) {
        if(str_contains($line,"\n")) $lineN++;
        Lexems::Tokenize($line, $tokens, $lineN);
        if(!Lexems::IsCommentWhite($line)) $tokens[] = [0, "EOL"]; //End of Line

    }
    Syntaxis::Verify($tokens);
    Generate::generate($tokens);
    fclose( $f );
    exit(0);