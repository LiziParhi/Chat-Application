<?php
    session_start();
    $username=$_POST['username'];
    $password = $_POST['password'];
    
    $con = mysql_connect('localhost' , 'root' , '');
    mysql_select_db('chatbox' , $con);
    $result = mysql_query("SELECT * FROM users WHERE username='$username' AND password='$password'");
                          
	if(mysql_num_rows($result)){
		$res=mysql_fetch_array($result);
		if ($res['active'] == "1") {
			echo "User already looged in.Please go to <a href='index.php'> back </a> and enter as a different user.<br/>";
			echo "You may register a new account by clicking <a href='register.php'>here</a>";
			echo "</center>";

		} else {
			$_SESSION['username']=$res['username'];
			$result = mysql_query("UPDATE users SET keepalive='10' WHERE username='$username'");
			$result = mysql_query("UPDATE users SET active='1' WHERE username='$username'");
			//echo "Set" . $username . $result;
			header("Location: index.php");
		}
	}
		else{
		echo "<center>";
		echo "No user found.Please go <a href='index.php'> back </a> and enter correct login.<br/>";
		echo "You may register a new account by clicking <a href='register.php'>here</a>";
		echo "</center>";
	}
    
?>
