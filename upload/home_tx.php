<?php
/**
 * 个人资料，用户签名
 */

	include './common/common.php';
	include 'logincheck.php';

	//修改头像
	if($_POST['profilesubmitbtn'])
	{
		$picture = upload('pic');
		$owner = dbUpdate('user', 'picture="'.$picture.'"', 'uid='.$_COOKIE['uid'].'');
		if($owner){
			setcookie('picture',$picture,2592000);
			header('location:home_tx.php');
		}else{
		$msg = '<font color=red><b>error，please contact the administrator</b></font>';
		$url = $_SERVER['HTTP_REFERER'];
		$style = 'alert_error';
		$toTime = 3000;
		include 'notice.php';
		exit;
	}
	}

	//读取头像
	$result = dbSelect('user','*', 'uid='.$_COOKIE['uid'].' and status!=2','',1);
	if(!$result)
	{
		$msg = '<font color=red><b>the user dose not exist or has been baned by the administrator</b></font>';
		$url = $_SERVER['HTTP_REFERER'];
		$style = 'alert_error';
		$toTime = 3000;
		include 'notice.php';
		exit;
	}
	$Jg = $result[0]['place'];
	
	
	$title = '用户签名 - '.WEB_NAME;
	include template("home_tx.html");

?>
