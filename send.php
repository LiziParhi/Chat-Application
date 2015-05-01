<?php
$mymessage = $_GET["name"];
$mysender= $_GET["sender"];
$myrec= $_GET["rec"];
$myFile = "chats.txt";
$metrics = "metrics.txt";
$check=0;
$cursor;
$fh = fopen($myFile, 'a') or die("can't open file");
$metrics = fopen($metrics, 'r+') or die("can't open file");
flock($fh, LOCK_EX);
fwrite($fh, $mymessage);
$stringData = "\r\n";
fwrite($fh, $stringData);
while (($line = fgets($metrics)) !== false) {
	$pieces = explode(" ",$line);
	$ribs = explode(":", (string)$pieces[0]);
	if (strcmp($myrec,$ribs[0]) == 0) {
		if (strcmp($mysender,$ribs[1]) == 0) {
			$cursor = ftell($metrics);
			$check=1;
			echo "Bellarusso:" . $cursor;
			break;
		}
	}
}
if($check == 0) {
	fseek($metrics,0,SEEK_END);
	fwrite($metrics,"\r\n$myrec" . ":" . "$mysender" . " 001" . " 000");
} 	else {
	$decrement = 0 - strlen($pieces[1]) - strlen($pieces[2]) - 1;
	$realval = intval($pieces[1]);
	$realval++;
	$hundred_digit = (int)($realval / 100);
	$ten_digit = (int)(($realval - ($hundred_digit*100)) / 10);
	$single_digit = (int)(($realval - ($hundred_digit*100) - ($ten_digit*10)));
	fseek($metrics,$decrement,SEEK_CUR);
	fwrite($metrics,"$hundred_digit");
	fwrite($metrics, "$ten_digit");
	fwrite($metrics, "$single_digit");
	echo $hundred_digit . $ten_digit . $single_digit;
	echo "done" . $realval;
}

flock($fh,LOCK_UN);
fclose($metrics);
fclose($fh);
?>
