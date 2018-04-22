<?php 

session_start();
header('Content-type: text/html; charset=utf-8');

require_once "common.php";
require_once "ToolsUser.php";

$toolsuser = new ToolsUser($db);
//check for if logged in
$result = $toolsuser->checkToken($_COOKIE['_urtn']);
if ($result['status'] == 'OK') {
	//header("Location: index.php");
	$_SESSION['user'] = $result['data'];
}
else {
	$_SESSION['user'] = null;
	setcookie("_urtn",'',1);
	
	if ($location == 'account') {
		header("Location: logout.php");
	}
}

?>
