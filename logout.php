<?php
	$username = $_GET["name"];
	$check = $_GET["check"];
	session_start();
    $con = mysql_connect('localhost' , 'root' , '');
    mysql_select_db('chatbox' , $con);
    $result = mysql_query("UPDATE users SET active='0' WHERE username='$username'");                  
	
    session_destroy();
    
?>

<html>
<head>
<title>Logged Out</title>
</head>
<style>
body {
		height: 100%;
		position: relative;
		font-family: Arial, Helvetica, sans-serif;
		font-size: 13px;
		line-height: 20px;
		min-width: 998px;
		background-image: url("images/BG.jpg");
	}
.ImageBorder
{
    border-width: 3px;
    border-color: Black;
}
</style>
<body>
<center>
<?php
if (strcmp($check, "0") == 0) {
		echo"<h1>Session logged out due to user inactivity. </h1>";
	} else {
		echo"<h1>Going Already? If you change your mind :)</h1>";
	}
?>
<a href ="index.php"><img src="signin.png" alt="Smiley face" height="100" width="100"></a><br></br>
<img src="byebye.jpg" class="ImageBorder" alt="Smiley face" height="700" width="500" border="5">
</center>
</body>
</html>

