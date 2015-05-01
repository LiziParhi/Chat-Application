<?php
$check = $_GET['check'];
$username = $_GET['name'];

	if (strcmp($check, "1") == 0){
		$con = mysql_connect('localhost' , 'root' , '');
		mysql_select_db('chatbox' , $con);

		$result = mysql_query("SELECT * FROM users WHERE username='$username'");
		if(mysql_num_rows($result)){
			$res=mysql_fetch_array($result);
			$friends_string = $res['friends'];
		}
		$result = mysql_query("SELECT * FROM users");
		$result_string = "";
		$image_string = "";
		$check_variable = 0;
		if (mysql_num_rows($result)){
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if ($check_variable != 0) {
					$result_string .= ":";
					$image_string .= ":";
				}
				$result_string .= $row["username"];
				$image_string .= $row["image"];
				$check_variable++;
			}
		}
		echo $result_string . "\r\n" ;
		echo $image_string . "\r\n" ;
		echo $friends_string . "\r\n";
	} else if (strcmp($check, "0") == 0) {
		$user = $_GET['user'];
		$con = mysql_connect('localhost' , 'root' , '');
		mysql_select_db('chatbox' , $con);

		$result = mysql_query("SELECT * FROM users WHERE username='$username'");
		if(mysql_num_rows($result)){
			$res=mysql_fetch_array($result);
			$friends_string = $res['friends'];
		}
		if (strlen($friends_string) > 0) {
			$friends_string .= ":" . $user;
		} else {
			$friends_string = $user;
		}
		$result = mysql_query("UPDATE users SET friends='$friends_string' WHERE username='$username'");  

		$result = mysql_query("SELECT * FROM users WHERE username='$user'");
		if(mysql_num_rows($result)){
			$res=mysql_fetch_array($result);
			$friends_string = $res['friends'];
		}
		if (strlen($friends_string) > 0) {
			$friends_string .= ":" . $username;
		} else {
			$friends_string = $username;
		}
		$result = mysql_query("UPDATE users SET friends='$friends_string' WHERE username='$user'"); 
	} else {
		$user = $_GET['user'];
		$con = mysql_connect('localhost' , 'root' , '');
		mysql_select_db('chatbox' , $con);

		$result = mysql_query("SELECT * FROM users WHERE username='$username'");
		if(mysql_num_rows($result)){
			$res=mysql_fetch_array($result);
			$friends_string = $res['friends'];
		}
		
		$friends_string_array = explode(":",$friends_string);
		$friends_string_temp = "";
		if ( count($friends_string_array) == 1){
			$friends_string = "";
		} else{
			$check_var = 0;
			for ($i = 0; $i < count($friends_string_array); $i++) {
				if (strcmp($user, $friends_string_array[$i]) == 0)
					continue;
				if ($check_var != 0) {
					$friends_string_temp .= ":";
				}
				$friends_string_temp .= $friends_string_array[$i];
				$check_var++;
			}
			$friends_string = $friends_string_temp;
		}
		$result = mysql_query("UPDATE users SET friends='$friends_string' WHERE username='$username'");  
		
		$result = mysql_query("SELECT * FROM users WHERE username='$user'");
		if(mysql_num_rows($result)){
			$res=mysql_fetch_array($result);
			$friends_string = $res['friends'];
		}
		
		$friends_string_array = explode(":",$friends_string);
		$friends_string_temp = "";
		echo "yahoo:" . $user . "\r\n";
		if (count($friends_string_array) == 1){
			$friends_string = "";
		} else{
			$check_var = 0;
			for ($i = 0; $i < count($friends_string_array); $i++) {
				if (strcmp($username, $friends_string_array[$i]) == 0)
					continue;
				if ($check_var != 0) {
					$friends_string_temp .= ":";
				}
				$friends_string_temp .= $friends_string_array[$i];
				$check_var++;
			}
			$friends_string = $friends_string_temp;
		}
		
		echo "mehhoo" . $friends_string_temp;
		$result = mysql_query("UPDATE users SET friends='$friends_string' WHERE username='$user'");
	}
?>
