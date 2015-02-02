<?php
	session_start();
	$email = $_REQUEST["email"];
	$pass = $_REQUEST["password"];

	if (!$email || empty($email)) die("Email field is required.");
	if (!$pass || empty($pass)) die("Password field is required.");

	$result = array(
		array('email'=>"james@fiu.edu",'password'=>'james', 'permission'=>"admin"),
		array('email'=>"carlos@gmail.com",'password'=>'test', 'permission'=>"admin"),
		array('email'=>"carlos@fiu.edu",'password'=>'test', 'permission'=>"labOP"),
		array('email'=>"luis@gmail.com",'password'=>'test', 'permission'=>"admin")
	);
	$resultlength = count($result)-1;
	$badAuthenthication = false;
	
	for ($i = 0; $i <= $resultlength; $i++) {
		if($result[$i]['email'] == $email && $result[$i]['password'] == $pass){
			$_SESSION["email"] = $result[$i]['email'];
			$_SESSION["permission"] = $result[$i]['permission'];
			$result = array('email'=>$_SESSION["email"], 'permission'=>$_SESSION["permission"]);
			$GLOBALS['badAuthenthication'] = true;
			break;
		}
	} 

	if($badAuthenthication!=1) die('Wrong email or password');
	else $return= null;
	$return_values = json_encode($result);
	header('Content-Type: application/json');
	echo $return_values;
?>
