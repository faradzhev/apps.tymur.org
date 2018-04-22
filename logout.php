<?php

session_start();
header('Content-type: text/html; charset=utf-8');

require_once "common.php";
require_once "ToolsUser.php";

$toolsuser = new ToolsUser($db);
$toolsuser->selectUser($_SESSION['user']['email']);
$toolsuser->terminateToken($_SESSION['user']['token']);

$_SESSION['user'] = null;
unset($_SESSION['user']);
setcookie("_urtn",'',0);

header("Location: login.php");

?>
