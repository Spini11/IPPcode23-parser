<?php
require_once('Lexer.php');
require_once('Syntaxis.php');
require_once('generate.php');
$f = fopen( 'php://stdin', 'r' );
while(!preg_match('/^\s*(?i:(.IPPcode23))(\s*$|#|\s*#)/', ($line = fgets($f))))
{
    if(!Lexems::IsCommentWhite($line))
        exit(21);
}
$tokens = [];
while( $line = fgets( $f ) ) {
    Lexems::Tokenize($line, $tokens);
    if(!Lexems::IsCommentWhite($line)) $tokens[] = [0, "EOL"];

}
Syntaxis::Verify($tokens);
Generate::generate3($tokens);
fclose( $f );
exit(0);