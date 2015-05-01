<?php
    if(isset($_POST['submit']) && isset($_FILES['fileToUpload'])){
        $con = mysql_connect('localhost' , 'root' , '');
		
        mysql_select_db('chatbox' , $con);
        $uname=$_POST['username'];
        $pword= $_POST['password'];
        $pword2=$_POST['password2'];
		
        $target_dir = "uploads/";
		$target_file = $target_dir . $uname . ".jpg";
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if($check !== false) {
			echo "File is an image - " . $check["mime"] . ".";
		} else {
			echo "File is not an image.";
			exit;
		}
		
		
		if (empty($uname) || empty($pword) || empty($pword2)) {
            echo "Username and Password cannot be left empty<br/>";

		}
        else if ($pword != $pword2){
            echo "Passwords do not match. <br/>";
        }
		else if (file_exists($target_file)) {
			echo "Sorry, file already exists.<br/>";
		}
		else if ($_FILES["fileToUpload"]["size"] > 5000000) {
			echo "Sorry, your file is too large.<br/>";
		}
		else if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
			&& $imageFileType != "gif" ) {
			echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br/>";
		}
        else{
            $checkexist =mysql_query("SELECT username FROM users WHERE username='$uname'");
            
            if(mysql_num_rows($checkexist)){
                echo "<center>";
                echo"Username already exists, please choose a different name<br/>";
                echo "</center>";
            }
            else{
				
				
				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
					mysql_query("INSERT INTO users(`username`,`password`,`image`) VALUES('$uname','$pword', '$target_file')");
					header("Location: login.html");
				} else {
					echo "<center>";
					echo"Sorry, there was an error uploading your file.<br/>";
					echo"Click<a href='register.html'> here </a> to register again<br/>";
					echo "</center>";

				}
            }
        }
    } else {
		echo "nothing sent";
		print_r($_FILES);
		 $con->close();
		exit;

	}
?>


