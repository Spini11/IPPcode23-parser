<?php
require_once('Lexer.php');
$f = fopen( 'php://stdin', 'r' );
while(($line = fgets($f)) != ".IPPcode23\n")
{
    if(!preg_match('/^\s*$/',$line) && !preg_match('/^\s*#/',$line))
        {
            exit(21);
        }
}
while( $line = fgets( $f ) ) {
    $delimiter = ' ';
    $words = explode($delimiter, $line);

}

fclose( $f );
