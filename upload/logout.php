<?php
/**
 * 注销
 */
	include './common/common.php';

	setcookie('uid','',time()-1);
	setcookie('username','""',time()-1);
	setcookie('udertype','',time()-1);
	setcookie('picture','',time()-1);
	setcookie('grade','',time()-1);

	$_SESSION['uid']='';
	$_SESSION['username']='';
	$_SESSION['udertype']='';

	$msg = '<font color=green><b>You have logged out，now you will visit as a tourist</b></font>';
	$url = 'index.php';
	$style = 'alert_right';
	$toTime = 3000;
	include 'notice.php';
