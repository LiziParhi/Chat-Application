<?php
$mysender= $_GET["sender"];
$myrec= $_GET["rec"];
$myFile = "chats.txt";
$metrics = "metrics.txt";
$check=0;
$metrics = fopen($metrics, 'r+') or die("can't open file");
while (($line = fgets($metrics)) !== false) {
	$pieces = explode(" ",$line);
	$ribs = explode(":", (string)$pieces[0]);
	if (strcmp($myrec,$ribs[0]) == 0) {
		echo $mysender . "*" . $ribs[1] . "*\r\n";
		if (strcmp($mysender,$ribs[1]) == 0) {
			$check=1;
			break;
		}
	}
}
if($check != 0) {
	$decrement = 0 - strlen($pieces[1]) - strlen($pieces[2]) - 1;
	fseek($metrics,$decrement,SEEK_CUR);
	fwrite($metrics,"000");
}
fclose($metrics);
?>
