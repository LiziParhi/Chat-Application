<?php
$currentUser = $_GET["name"];
$rec= $_GET["name2"];
$metrics = "metrics.txt";
$metrics = fopen($metrics, 'r') or die("can't open file");
flock($metrics,LOCK_EX);
while (($line = fgets($metrics)) !== false) {
	$pieces = explode(" ",$line);
	$ribs = explode(":", (string)$pieces[0]);
	$realval = intval($pieces[1]);
	$readval = intval($pieces[2]);
	$diff = $realval - $readval;
	if ($diff){
		if (strcmp($rec,$ribs[0]) == 0){
			if ($currentUser) {
				if (strcmp($currentUser, $ribs[1]) != 0) {
					echo "$ribs[1]" . ":" . "$diff" . "\r\n";
				}
			} else {
				echo "$ribs[1]" . ":" . "$diff" . "\r\n";
			}
		}
	}
}
flock($metrics,LOCK_UN);
fclose($metrics);
?>
