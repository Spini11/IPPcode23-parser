<?php
    require_once('Lexer.php');
    require_once('Syntaxis.php');
    require_once('generate.php');
    require_once('ErrorHandling.php');
    $f = fopen( 'php://stdin', 'r' );
    $lineN = 0;
    while(!preg_match('/^\s*(?i:(.IPPcode23))(\s*$|#|\s*#)/', ($line = fgets($f))))
    {
        if(str_contains($line,"\n")) $lineN++;
        if(!Lexems::IsCommentWhite($line))
            ErrorHandling::ErrorHeader($lineN);
    }
    $lineN++;
    $tokens = [];
    while( $line = fgets( $f ) ) {
        if(str_contains($line,"\n")) $lineN++;
        Lexems::Tokenize($line, $tokens, $lineN);
        if(!Lexems::IsCommentWhite($line)) $tokens[] = [0, "EOL"];

    }
    Syntaxis::Verify($tokens);
    Generate::generate3($tokens);
    fclose( $f );
    exit(0);