<?php
$myFile = "chats.txt";
$mymessage = $_GET["name"];
$request_pieces = explode(":",$mymessage);
$fh = fopen($myFile, 'r') or die("can't open file");
flock($fh,LOCK_EX);
while (($line = fgets($fh)) !== false) {
	$pieces = explode(":",$line);
	$pieces[3] = str_replace(array("\n", "\r"), '', $pieces[3]);
	if (strcmp($pieces[2], $request_pieces[0])== 0){
		if (strcmp($pieces[3], $request_pieces[1]) == 0){
			echo $request_pieces[0] . ": " . $pieces[1] . "\r\n";
		}
	}
	if (strcmp($pieces[2], $request_pieces[1])== 0) {
		if (strcmp($pieces[3], $request_pieces[0]) == 0) {
			echo $request_pieces[1] . ": "  . $pieces[1] . "\r\n";
		}
	}
}
flock($fh,LOCK_UN);
fclose($fh);
?>
