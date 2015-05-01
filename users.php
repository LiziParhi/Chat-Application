<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chatbox";
$mymessage = $_GET["name"];
$check = $_GET["check"];
// Create connection

$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
     echo "Connection failed: " . $conn->connect_error;
	 return;
}
$sql_friends = "SELECT * FROM users WHERE username='$mymessage'";
$friend_result = $conn->query($sql_friends);
if (!$friend_result) {
	echo "Could not query:" . mysql_error();
	return;
}
$row = $friend_result->fetch_assoc();
$friend_array = explode(":",$row["friends"]);

$sql = "SELECT username, active, image FROM users";
$result = $conn->query($sql);
if (!$result) {
	echo "Could not query:" . mysql_error();
	return;
}
if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {
		if (strcmp($check,"0") == 0) {
			$value = ($row["active"]==0) ? "Inactive" : "Active";
			$bing = '\'' . $row["username"] . '\'';
			if ($row["username"] == $mymessage)
				 continue;
			
			$friend_check = 0;
			for ($i = 0; $i < count($friend_array); $i++) {
				if (strcmp($friend_array[$i],$row["username"]) == 0) {
					$friend_check = 1;
					break;
				}
			}
			
			if (!$friend_check){ continue;}
			 
			else if ($row["active"]==1) {
				echo "1\r\n";
				echo $row["username"]. "\r\n";
			} else {
				echo "0\r\n";
				echo $row["username"]. "\r\n";
			}
		} else {
			$admin = $_GET["admin"];
			if ($row["username"] == $mymessage) {
				echo "user:" . $row["image"]. "\r\n";
			} else if ($row["username"] == $admin) {
				echo "owner:" . $row["image"]. "\r\n";
			}
		}
	}
}
$conn->close();
?> 
