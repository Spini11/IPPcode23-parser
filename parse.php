<?php
require_once('Lexer.php');
$f = fopen( 'php://stdin', 'r' );
while(($line = fgets($f)) != ".IPPcode23\n")
{
    if(!Lexems::IsCommentWhite($line))
        exit(21);
}
$tokens = [];
while( $line = fgets( $f ) ) {
    Lexems::Tokenize($line, $tokens);
    print_r($tokens);
}

fclose( $f );
