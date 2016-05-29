<?php

session_start();


if($_SESSION['EMAIL'])
	echo "WELCOME, ".$_SESSION['EMAIL']."! <br> <a href='logout.php'> Logout</a>";
else
	die("You must log in first");
?>
