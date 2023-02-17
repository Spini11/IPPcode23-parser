<?php
require_once('Lexer.php');
$f = fopen( 'php://stdin', 'r' );
while(!preg_match('/^\s*.IPPcode23(\s*$|#|\s*#)/', ($line = fgets($f))))
{
    if(!Lexems::IsCommentWhite($line))
        exit(21);
}
$tokens = [];
while( $line = fgets( $f ) ) {
    Lexems::Tokenize($line, $tokens);
    $tokens[] = [0, "EOL"];

}
print_r($tokens);
print("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
print("<program language=\"IPPcode23\"/>\n");
//generate code
fclose( $f );
exit(0);