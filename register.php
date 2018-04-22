<?php

session_start();
header('Content-type: text/html; charset=utf-8');

require_once "common.php";
require_once "ToolsUser.php";

if ($_POST['email']) {
	$toolsuser = new ToolsUser($db);
	$result = $toolsuser->register($_POST['name'],$_POST['email'],$_POST['password'],$_POST['password_repeat']);
	if ($result['code'] == 200) {
		$result = $toolsuser->login($_POST['email'],$_POST['password']);
		//print_r($result);
		if ($result['code'] == 200) {
			$_SESSION['user'] = $result['data'];
			$_SESSION['new_user'] = "true";
			setcookie("_urtn",$result['data']['token'],(($_POST['remember'])?$result['data']['expire']:(time()+strtotime("+1 day"))));
			header("Location: index.php");
		}
		else {
			$_SESSION['login_error'] = $result['status'];
		}
	}
	else {
		$_SESSION['login_error'] = $result['status'];
	}
}

header("Location: login.php"); 


?>
