<?php
    session_start();
	$server_status;
	$my_pic;
	
    if(!isset($_SESSION['username'])){
		header("Location: login.html");
		exit;
	} else {
		$username = $_SESSION['username'];
		$con = mysql_connect('localhost' , 'root' , '');
		mysql_select_db('chatbox' , $con);
		
		$result = mysql_query("SELECT * FROM users WHERE username='$username'");
		if(mysql_num_rows($result)){
			$res=mysql_fetch_array($result);
			$my_pic = $res['image'];
			if ($res['active'] == "0") {
				header("Location: logout.php?name='$username'&check=0");
			}
		}
	}
?>

<html>
<head>
<meta charset="UTF-8">
<title>Chat Box </title>
<link rel="stylesheet" type="text/css" href="index.css">
</head>
<body>
<div id="thelowerdiv">
</div>
<div id="wrappertop"></div>
<div id="alloverdiv">
</div>


<script>
/*
 * Variable  declarations 
 */
var messageinput;
var currentUser;
var chatheaderdiv = null;
var chatheaderuser;
var chatheaderadmin;
var textDiv;
var mytimer;
var mytimer1;
var mytimer2;
var alloverdiv;
var thelowerdiv;
var upHeader;
var welcomeDiv;
var welcomeDivimg;
var welcomeDivfooter;
var allUsers;
var leftDiv;
var newDiv;
var chatheader;
var textDiv;
var send;
var lastuser;
var lastmessages = 0;
var lastuser = null;
var currentuserimage;
var showUsersdiv;
var toggle_friends = 0;
var _idleTimeout = 10000;
var _awayTimeout = 6000000;
var _idleTimer = null;
var _awayTimer = null;
var image_src_admin="<?=$my_pic?>";
var image_src_user="uploads/anshuman.png";

// Timeout handlers
document.onkeydown = myKeyDownHandler;
document.onmousemove = myMouseMoveHandler;
_idleTimer = setTimeout(_makeIdle, _idleTimeout);
_awayTimer = setTimeout(_makeIdle, _awayTimeout);


// Timer for keepalives to server, usefull to map multiple friends
mytimer2 = setInterval(keepaliveuser, 3000);

/*
 * Constructors 
 */
alloverdiv = document.getElementById("alloverdiv");
thelowerdiv = document.getElementById("thelowerdiv");

//Constructing friendspace
leftDiv = document.createElement("div");
leftDiv.id = "leftDiv";
allUsers = document.createElement("div");
allUsers.id = "allusers";
upHeader = document.createElement("div");
welcomeDiv = document.createElement("div");
upHeader.id = "upHeader";
upHeader.innerHTML = "<h2> FriendSpace </h2>\n";
welcomeDiv.id = "welcomeDiv";
welcomeDivimg = document.createElement("img");
welcomeDivimg.setAttribute("src", image_src_admin);
welcomeDivimg.setAttribute("height", "50");
welcomeDivimg.setAttribute("width", "50");
welcomeDivimg.setAttribute("alt", "bingo");
welcomeDivfooter = document.createElement("img");
welcomeDivfooter.setAttribute("src", "images/wrapper_top.png");
welcomeDivfooter.setAttribute("height", "3");
welcomeDivfooter.setAttribute("width", "180");
welcomeDivfooter.setAttribute("alt", "bingo");
welcomeDiv.appendChild(welcomeDivimg);
welcomeDiv.innerHTML += " <p> Hello <br> <?=$_SESSION['username']?> </p>";
welcomeDiv.appendChild(welcomeDivfooter);
var friendsdiv = document.createElement("div");
friendsdiv.innerHTML = "<h3>FRIENDS</h3>";
friendsdiv.setAttribute("style","height:30px;text-align:center");
welcomeDiv.appendChild(friendsdiv);
leftDiv.appendChild(upHeader);
leftDiv.appendChild(welcomeDiv);
leftDiv.appendChild(allUsers);
alloverdiv.appendChild(leftDiv);

//Constructing chatspace
newDiv = document.createElement("div");
newDiv.id = "chatwindow";
chatheader = document.createElement("div");
chatheader.id = "chatheader";
chatheader.setAttribute('class', 'chatheader_inactive');
chatheaderleftdiv = document.createElement("div");
chatheaderleftdiv.id = "chatheaderleftdiv";
chatheaderrightdiv = document.createElement("div");
chatheaderrightdiv.id = "chatheaderrightdiv";
chatheaderrightdiv.innerHTML = "<a href=\"logout.php?name=<?=$_SESSION['username']?>&check=1\" style=\"text-decoration:none;color: inherit\" ><h1>logout</h1></a>";
chatheader.appendChild(chatheaderleftdiv);
chatheader.appendChild(chatheaderrightdiv);
textDiv = document.createElement("div");
textDiv.id = "textwindow";
messageinput = document.createElement('TEXTAREA');
messageinput.id = "message";
messageinput.setAttribute("type", "text");
send = document.createElement('input');
send.id = "send";
send.setAttribute("type", "button");
send.value=">";
send.onclick=sendMessage;
messageinput.disabled=true;
send.disabled=true;
messageinput.value = "   Select a friend to start chatting...";
newDiv.appendChild(chatheader);
newDiv.appendChild(textDiv);
newDiv.appendChild(messageinput);
newDiv.appendChild(send);
alloverdiv.appendChild(newDiv);

//Constructing the Add friends plane
showUsersdiv = document.createElement("div");
showUsersdiv.id = "showUsersdiv";
Usersclose = document.createElement("div");
Usersclose.id = "Usersclose";
Usersclose.innerHTML = "+ Add Friends";
showUsersdiv.appendChild(Usersclose);
document.body.appendChild(showUsersdiv);

/*
 * startup code
 */

//Update users and messages
updateUsers();
checkOtherMessages();

// set timer to update friends status every 3 seconds
mytimer1 = setInterval(updateUsers, 3000);

//Populate registered users but not friends
showUsersdiv.onclick = function() {
		openUsers();
};

/*
 * Functions for Idle and Away Timeouts
 */

function myKeyDownHandler() {
	if (_idleTimer != null) {
        clearTimeout (_idleTimer);
    }
	if (_awayTimer != null) {
        clearTimeout (_awayTimer);
    }
	document.body.style.backgroundColor = "#FFEFD5";
	_idleTimer = setTimeout(_makeIdle, _idleTimeout);
	_awayTimer = setTimeout(_makeAway, _awayTimeout);
	//console.log("Happening");
	thelowerdiv.innerHTML = "";
}

function myMouseMoveHandler() {
	if (_idleTimer != null) {
        clearTimeout (_idleTimer);
    }
	if (_awayTimer != null) {
        clearTimeout (_awayTimer);
    }
	document.body.style.backgroundColor = "white";
	alloverdiv.style.opacity=1;
	showUsersdiv.style.opacity=1;
	_idleTimer = setTimeout(_makeIdle, _idleTimeout);
	_awayTimer = setTimeout(_makeAway, _awayTimeout);
	//console.log("Happening set awaytimer to 60 seconds from now");
	thelowerdiv.innerHTML = "";
}

function _makeIdle() {
	document.body.style.backgroundColor = "black";
	alloverdiv.style.opacity=0.4;
	showUsersdiv.style.opacity=0.4;
	console.log("Fired idle timer");
	var elem = document.createElement("img");
	elem.setAttribute("src", "sleeping.jpg");
	elem.setAttribute("height", "300");
	elem.setAttribute("width", "300");
	elem.setAttribute("alt", "Flower");
	elem.setAttribute("id", "someimage");
	thelowerdiv.innerHTML = "";
	thelowerdiv.appendChild(elem);
	thelowerdiv.innerHTML += "<br><h2>Purrrr...</h2></br>";
}

function _makeAway() {
	console.log("Fired away timer");
	window.location.assign("logout.php?name=<?=$_SESSION['username']?>&check=0");
}

/*
 * Main functions:
 * 1. send_message() : send messages to server
 * 2. updateMessages() : update messages for current friend
 * 3. checkOtherMessages() : check messages for other users and set notifications
 * 4. myzero() : onselect function to open chatbox for selected friend
 * 5. setmessage() : send message read notification to server
 * 6. updateUsers() : update list of users and their status
 * 7. keepaliveuser() : send client keepalive notifications to server
 * 8. openUsers() : open the pane for potential friends
 * 9. addFriends() : Add a friend to your friendlist
 * 10. closeUsers(): close the pane for potential friends
 * 11. removeFriend() : Remove a friend fro your list
 */

function sendMessage(){
	var hr = new XMLHttpRequest();
	var url = "send.php";
	if (messageinput.value == '') {
		console.log("no input");
		return;
	}

	var urlencodedmsg = Base64.encode(messageinput.value);
	console.log(urlencodedmsg);
	var mydata = "message:"+ urlencodedmsg + ":" + "<?=$_SESSION['username']?>" + ":" +  currentUser;
	var params="name="+mydata+"&sender="+"<?=$_SESSION['username']?>"+"&rec="+currentUser;
	hr.open("POST", url+"?"+params, true);
	hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	hr.onreadystatechange = function() {
		if(hr.readyState == 4 && hr.status == 200) {
			console.log("happening\n" + hr.responseText);
			updateMessages();
		}
	}
	messageinput.value='';
	hr.send(null);
}

function updateMessages(){
	var hr1 = new XMLHttpRequest();
	var url = "getmessage.php";
	var url1 = "users.php";
	var mydata="<?=$_SESSION['username']?>"+":"+currentUser;
	var params="name="+mydata;
	var response;
	var response_pieces;
	var messagediv;
	var messagediv_rightside_header;
	var messagediv_rightside_text;
	var messagediv_rightside_user;
	var messagedivpic;
	var checkimage = 0;
	var i;
	
	hr1.open("POST", url1+"?"+"name="+currentUser+"&check=1"+"&admin="+"<?=$_SESSION['username']?>", true);
	hr1.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	hr1.onreadystatechange = function() {
		if(hr1.readyState == 4 && hr1.status == 200) {
			var user_count_pieces = hr1.responseText.split("\r\n");
			for (var i = 0; i < user_count_pieces.length; i++) {
				if (user_count_pieces[i]){
					var user_pieces = user_count_pieces[i].split(":");
					if (user_pieces[0] == "owner") {
						image_src_admin = user_pieces[1];
					}
					else if (user_pieces[0] == "user") {
						image_src_user = user_pieces[1];
					}
				}
			}
			
			var hr = new XMLHttpRequest();
				
			hr.open("POST", url+"?"+params, true);
			hr.onreadystatechange = function() {
				if(hr.readyState == 4 && hr.status == 200) {
					response = hr.responseText.split("\r\n");
					textDiv.innerHTML = "";
					for (i =0; i < response.length; i++) {
						if (response[i]) {
							response_pieces = response[i].split(":");
							messagediv = document.createElement("div");
							var elem = document.createElement("img");
							//messagedivtext.innerHTML = response_pieces[0] + ":\r\n<br>" + response_pieces[1];
							//messagediv.innerHTML = response[i] + "\r\n<br>";
							if (response_pieces[0] == "<?=$_SESSION['username']?>") {
								messagediv_rightside_header = document.createElement("div");
								messagedivpic = document.createElement("div");
								messagediv_rightside_header = document.createElement("div");
								messagediv_rightside_user = document.createElement("div");
								messagediv_rightside_user.setAttribute("class","msguser");
								messagediv_rightside_text = document.createElement("div");
								messagediv_rightside_header.setAttribute("class","messageright");
								elem.setAttribute("src", image_src_admin);
								elem.setAttribute("height", "30");
								elem.setAttribute("width", "30");
								elem.setAttribute("alt", "bingo");
								messagedivpic.appendChild(elem);
								messagedivpic.setAttribute("class","messagepic");
								messagediv_rightside_header.appendChild(messagedivpic);
								messagediv_rightside_user.innerHTML = response_pieces[0];
								messagediv_rightside_header.appendChild(messagediv_rightside_user);
								messagediv_rightside_text.innerHTML = Base64.decode(response_pieces[1]);
								messagediv_rightside_text.setAttribute("class","msgtextright");
								messagediv.appendChild(messagediv_rightside_header);
								messagediv.appendChild(messagediv_rightside_text);
								messagediv.setAttribute("class","newstyle");
							}
							else {
								messagediv_rightside_header = document.createElement("div");
								messagedivpic = document.createElement("div");
								messagediv_rightside_header = document.createElement("div");
								messagediv_rightside_user = document.createElement("div");
								messagediv_rightside_user.setAttribute("class","msguser");
								messagediv_rightside_text = document.createElement("div");
								messagediv_rightside_header.setAttribute("class","messageleft");
								elem.setAttribute("src", image_src_user);
								elem.setAttribute("height", "30");
								elem.setAttribute("width", "30");
								elem.setAttribute("alt", "bingo");
								messagedivpic.appendChild(elem);
								messagediv_rightside_header.appendChild(messagedivpic);
								messagedivpic.setAttribute("class","messagepic");
								messagediv_rightside_user.innerHTML = response_pieces[0];
								messagediv_rightside_header.appendChild(messagediv_rightside_user);
								messagediv_rightside_text.innerHTML = Base64.decode(response_pieces[1]);
								messagediv_rightside_text.setAttribute("class","msgtextleft");
								messagediv.setAttribute("class","newstyle");
								messagediv.appendChild(messagediv_rightside_header);
								messagediv.appendChild(messagediv_rightside_text);
							}
							textDiv.appendChild(messagediv);
						}
					}
					chatheaderleftdiv.innerHTML = "";
					var chatheaderimg = document.createElement("img");
					chatheaderimg.setAttribute("src", image_src_user);
					chatheaderimg.id = "innerimg";
					chatheaderimg.setAttribute("height", "50");
					chatheaderimg.setAttribute("width", "50");
					chatheaderimg.setAttribute("alt", "bingo");
					chatheaderleftdiv.appendChild(chatheaderimg);
					chatheaderdiv = document.createElement("div");
					chatheaderdiv.id = "chatheaderdiv";
					chatheaderdiv.innerHTML += currentUser;
					chatheaderleftdiv.appendChild(chatheaderdiv);
					if (lastuser == currentUser){
						if (lastmessages != i) {
							setmessage();
							textDiv.scrollTop = textDiv.scrollHeight;
						}
					} else {
						textDiv.scrollTop = textDiv.scrollHeight;
					}
					lastuser = currentUser;
					lastmessages = i;
				}
			}
			hr.send(null);
		}
	}
	hr1.send(null);
	
	
}

function checkOtherMessages() {
	var hr = new XMLHttpRequest();
	var url = "othermessages.php";
	var params="name="+currentUser+"&name2="+"<?=$_SESSION['username']?>";
	
	hr.open("POST", url+"?"+params, true);
	hr.onreadystatechange = function() {
		if(hr.readyState == 4 && hr.status == 200) {
			var user_counts = 	hr.responseText;
			//console.log(user_counts);
			var user_count_pieces = user_counts.split("\r\n");
			for (var i = 0; i < user_count_pieces.length; i++) {
				if (user_count_pieces[i]){
					user_pieces = user_count_pieces[i].split(":");
					//console.log("DING: "  + user_pieces[0] + "="  + user_pieces[1]);
					var getrow = document.getElementById(user_pieces[0] + "cell");
					getrow.innerHTML = "<i><span style=\"color:red;font-size:15px;text-align:left\">" + user_pieces[1] + " </span></i><img src=\"Mail-icon.png\" alt=\"Smiley face\" height=\"18\" width=\"15\">";
				}
			}
		}
	}
	hr.send(null);
}

function myzero(bingo, check_disabled) {
	
	mytimer = setInterval(updateMessages, 3000);
	currentUser = bingo;
	
	if (check_disabled == 0) {
		messageinput.disabled=false;
		messageinput.value = "";
		messageinput.placeholder = currentUser +  " is offline... Messages will be read when online";
		send.disabled=false;
		//messageinput.value = "   " + bingo + " is offline";
		messageinput.style.fontStyle="normal";
		//console.log("coming to disabled");
		chatheaderrightdiv.setAttribute("style", "color:#58ACFA;");
		chatheader.setAttribute('class', 'chatheader_inactive');
	} else {
		messageinput.disabled=false;
		messageinput.value = "";
		messageinput.placeholder = "Type here...";
		send.disabled=false;
		chatheaderrightdiv.setAttribute("style", "color:#009A00;");
		messageinput.value="";
		console.log("coming to enabled");
		chatheader.setAttribute('class', 'chatheader_active');
	}
	//send = document.getElementById("send");
	messageinput.onkeypress = function(event) {
		if (event.keyCode == 13) {
			event.preventDefault();
			sendMessage();
		}
	};
	
	updateMessages();
	setmessage();

}

function setmessage() {
	var hr = new XMLHttpRequest();
	var url = "setmessage.php";
	var params="sender="+currentUser+"&rec="+"<?=$_SESSION['username']?>";
	hr.open("POST", url+"?"+params, true);
	hr.onreadystatechange = function() {
		if(hr.readyState == 4 && hr.status == 200) {
			updateUsers();
		}
	}
	hr.send(null);
}


function updateUsers() {
	var hr = new XMLHttpRequest();
	var url = "users.php";
	var params="name="+"<?=$_SESSION['username']?>"
	var response;
	hr.open("POST", url+"?"+params+"&check=0", true);
	hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	hr.onreadystatechange = function() {
		if(hr.readyState == 4 && hr.status == 200) {
			allUsers = document.getElementById("allusers");
			if (document.getElementById("allusers") == null){
				allUsers = document.createElement("div");
				alloverdiv.appendChild(allUsers);
				allUsers.id = "allusers";
			}
			allUsers.innerHTML = "";
			var t1 = document.createElement('table');
			t1.setAttribute("style", "width:100%");
			response = hr.responseText.split("\r\n");
			for (var i =0; i < response.length; i +=2) {
				var indiv = document.createElement("div");
				var indiv1 = document.createElement("div");
				var indiv2 = document.createElement("div");
				var indiv3 = document.createElement("div");
				indiv.setAttribute("class", "users");
				indiv1.setAttribute("class", "users_text");
				indiv2.setAttribute("class", "users_indicator");
				indiv3.setAttribute("class", "users_remove");
				indiv3.innerHTML = "<img class=\"usrimages\" src=\"DeleteRed.png\" alt=\"Smiley face\" height=\"15\" width=\"15\">";
				if (i== 0) {
					indiv.setAttribute("style", "border-top:1px solid #778899");
				}
				if (response[i] == "1") {
					indiv1.innerHTML = "<img class=\"usrimages\" src=\"Green-orb.png\" alt=\"Smiley face\" height=\"30\" width=\"20\">"+ response[i+1];
					indiv1.addEventListener("click", function() {
						var newvar = this.innerHTML.split('\">');
						console.log("checking:*" + newvar[1].trim() + "*");
						//var newvar2 = newvar[].split('</span>');
						//console.log("checking " + newvar2[0]);
						myzero(newvar[1].trim(), 1);
					}, false);
					if (currentUser	== 	response[i+1]) {
						if (chatheaderdiv != null) {
								//if (messageinput.disabled) {
								//	messageinput.value = "";
								//}
								//messageinput.disabled=false;
								//send.disabled=false;
								chatheader.setAttribute('class', 'chatheader_active');
								messageinput.placeholder = "Type here...";
						}
					}
					indiv3.id = response[i+1] + "removecell";
					indiv3.addEventListener("click", function() {
						var newvar = this.id.split('removecell');
						console.log("checking:*" + newvar[0].trim() + "*");
						//var newvar2 = newvar[].split('</span>');
						//console.log("checking " + newvar2[0]);
						removeFriend(newvar[0].trim(), 1);
					}, false);
					indiv2.id = response[i+1] + "cell";
					indiv.appendChild(indiv1);
					indiv.appendChild(indiv2);
					indiv.appendChild(indiv3);
					allUsers.appendChild(indiv);
				} else if (response[i] == "0"){
					indiv1.innerHTML = "•    " + response[i+1];
					indiv2.id = response[i+1] + "cell";
					indiv3.id = response[i+1] + "removecell";
					indiv1.addEventListener("click", function() {
						var newvar = this.innerHTML.split('• ');
						//var newvar2 = newvar[1];
						console.log("checking:*" + newvar[1].trim() + "*");
						//console.log("checking " + newvar2[0]);
						myzero(newvar[1].trim(), 0);
					}, false);
					if (currentUser	== 	response[i+1]) {
						if (chatheaderdiv != null) {
								//messageinput.disabled=true;
								//send.disabled=true;
								//messageinput.value = "";// + currentUser + " is offline";
								chatheader.setAttribute('class', 'chatheader_inactive');
								messageinput.placeholder = currentUser +  " is offline... Messages will be read when online";
						}
					}
					indiv3.addEventListener("click", function() {
						var newvar = this.id.split('removecell');
						//var newvar2 = newvar[1];
						console.log("checking:*" + newvar[0].trim() + "*");
						//console.log("checking " + newvar2[0]);
						removeFriend(newvar[0].trim(), 0);
					}, false);
					indiv.appendChild(indiv1);
					indiv.appendChild(indiv2);
					indiv.appendChild(indiv3);
					allUsers.appendChild(indiv);
				} else {
					continue;				
				}
			}
			checkOtherMessages();
			//alert(hr.responseText);
		}
	}
	hr.send(null);
}

function keepaliveuser() {
	var hr = new XMLHttpRequest();
	var url = "keepalive.php";
	var params="name="+"<?=$_SESSION['username']?>";
	
	hr.open("POST", url+"?"+params, true);
	hr.onreadystatechange = function() {
		if(hr.readyState == 4 && hr.status == 200) {
		}
	}
	hr.send(null);

}

function openUsers(){
	showUsersdiv.innerHTML = "";
	Usersdiv = document.createElement("div");
	Usersdiv.id = "Usersdiv";
	Usersclose = document.createElement("div");
	Usersclose.id = "Usersclose";
	Usersclose.innerHTML = "Close";
	showUsersdiv.appendChild(Usersdiv);
	showUsersdiv.onclick = function() {
		closeUsers();
	};
	showUsersdiv.appendChild(Usersclose);
	document.body.appendChild(showUsersdiv);
	
	var hr = new XMLHttpRequest();
	var url = "newfriends.php";
	var params="name="+"<?=$_SESSION['username']?>"+"&check=1";
	
	hr.open("POST", url+"?"+params, true);
	hr.onreadystatechange = function() {
		if(hr.readyState == 4 && hr.status == 200) {
			//console.log(hr.responseText);
			var allineed = hr.responseText.split("\r\n");
			var friends_string = allineed[2];
			var friends_array;
			if (friends_string) {
				friends_array = friends_string.split(":");
			}
			var user_string = allineed[0];
			var user_array = user_string.split(":");
			var image_string = allineed[1];
			var image_array = image_string.split(":");
			for (var i =0; i < user_array.length; i++) {
				var check_my_user = 0;
				if (friends_string) {
					for (var j = 0; j < friends_array.length; j++) {
						if (user_array[i] == friends_array[j]){
							check_my_user=1;
							break;
						}
					}
				}
				if (user_array[i] == "<?=$_SESSION['username']?>") {
					check_my_user=1;
				}
				if (check_my_user == 0){
					var newfriend = document.createElement("div");
					newfriend.setAttribute("class","newfriend");
					var newfriendimg = document.createElement("img");
					newfriendimg.setAttribute("src", image_array[i]);
					newfriendimg.setAttribute("height", "50");
					newfriendimg.setAttribute("width", "50");
					newfriendimg.setAttribute("alt", "bingo");
					newfriend.appendChild(newfriendimg);
					newfriend.innerHTML += "<br>" + user_array[i];
					newfriend.id = "pinky"+user_array[i];
					newfriend.onclick = function () {
						var newvar = this.id.split('pinky');
						addFriends(newvar[1]);
					};
					Usersdiv.appendChild(newfriend);
				}
			}
		}
	}
	hr.send(null);
}

function addFriends(bingo) {
	var hr = new XMLHttpRequest();
	var url = "newfriends.php";
	//console.log("and this is" + bingo);
	var params="name="+"<?=$_SESSION['username']?>"+"&check=0&user="+bingo;
	hr.open("POST", url+"?"+params, true);
	hr.onreadystatechange = function() {
		if(hr.readyState == 4 && hr.status == 200) {
			//console.log("bing" + hr.responseText);
			closeUsers();
			openUsers();
			updateUsers();
		}
	}
	hr.send(null);
}

function closeUsers() {
	showUsersdiv.innerHTML = "";
	Usersclose = document.createElement("div");
	Usersclose.id = "Usersclose";
	Usersclose.innerHTML = "+ Add Friends";
	showUsersdiv.onclick = function() {
		openUsers();
	};
	showUsersdiv.appendChild(Usersclose);
	document.body.appendChild(showUsersdiv);
}

function removeFriend(bingo){
	var hr = new XMLHttpRequest();
	var url = "newfriends.php";
	console.log("and this is" + bingo);
	var params="name="+"<?=$_SESSION['username']?>"+"&check=2&user="+bingo;
	hr.open("POST", url+"?"+params, true);
	hr.onreadystatechange = function() {
		if(hr.readyState == 4 && hr.status == 200) {
			console.log("bing" + hr.responseText);
			closeUsers();
			openUsers();
			updateUsers();
		}
	}
	hr.send(null);
}

/*
 * Main functions:
 * Base64 class to encode and decode messages sent by users
 */

var Base64 = {
    _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

    encode: function(input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;

        input = Base64._utf8_encode(input);

        while (i < input.length) {

            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output + this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) + this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

        }

        return output;
    },


    decode: function(input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        while (i < input.length) {

            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }

        }

        output = Base64._utf8_decode(output);

        return output;

    },

    _utf8_encode: function(string) {
        string = string.replace(/\r\n/g, "\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if ((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    },

    _utf8_decode: function(utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while (i < utftext.length) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if ((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i + 1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i + 1);
                c3 = utftext.charCodeAt(i + 2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        }

        return string;
    }

}

</script>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="styl1.css"/>

<script>
</body>
</html>
