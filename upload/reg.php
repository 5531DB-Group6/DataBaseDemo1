<?php
/**
 * 注册
 */
	include './common/common.php';

	$title = 'Register - ' . WEB_NAME;

	//验证是否为提交注册信息
	if (!empty($_POST['regsubmit']))
	{
		$uname = strMagic($_POST['username']);
		$upass = trim($_POST['password']);
		$urpass = trim($_POST['repassword']);
		$mfirstname = trim($_POST['memberfirstname']);
		$email = $_POST['mail'];
		$pyzm = $_POST['yzm'];
		
		//错误跳转页默认值
		$url = $_SERVER['HTTP_REFERER'];
		$style = 'alert_error';
		$toTime = 3000;

		$alterNotice = false;	//提示页面标记位
		//验证用户名长度
		if(stringLen($uname))
		{
			$alterNotice = true;
			$msgArr[] = '<font color=red><b>Wrong length of username：consist of 3 to 12 characters</b></font>';
		}

		//验证email
		if(checkEmail($email))
		{
			$alterNotice = true;
			$msgArr[] = '<font color=red><b>Error：not valid email address</b></font>';
		}

		//判断数据库里是否存在这个用户名
		$exists = dbSelect('user','uid', 'username="'.$uname.'"','uid desc',1);
		if($exists)
		{
			$alterNotice = true;
			$msgArr[] = '<font color=red><b>username has been used</b></font>';
		}
		
		//验证密码长度
		if(stringLen($upass))
		{
			$alterNotice = true;
			$msgArr[] = '<font color=red><b>Wrong length of password：consists of 3 to 12 characters</b></font>';
		}
		
		//验证两次密码是否一致
		if(str2Equal($upass, $urpass))
		{
			$alterNotice = true;
			$msgArr[] = '<font color=red><b>Error：passwords are not identical</b></font>';
		}

		//check whether the entered first name of an existing member is correct
		$FNexists = dbSelect('user','uid', 'firstname="'.$mfirstname.'"','uid desc',1);
		if (!$FNexists){
			$alterNotice = true;
			$msgArr[] = '<font color=red><b>Error：cannot find the corresponding first name</b></font>';
		}

		//验证是否需要显示提示信息
		if($alterNotice)
		{
			$msg = join('<br />', $msgArr);
			include 'notice.php';
			exit;
		}

		//创建用户
		//$money = REWARD_REG;
		$monthlater=time()+60*60*24*30;
		//echo $monthlater;
		$n = 'username, password, email, udertype, regtime, lasttime, expiretime';
		//$v = "'$uname', '".md5($upass)."', '$email', 0, ".time().", ".time().", ".ip2long($_SERVER['REMOTE_ADDR']).", ".$money;
		$v = "'$uname','".md5($upass)."', '$email', 0, ".time().", ".time().", "."$monthlater";
		$result = dbInsert('user', $n, $v);
		if(!$result)
		{
			$msg = '<font color=red><b>Fail to register, pleas contact the administrator</b></font>';
			include 'notice.php';
		}else{
			//注册成功后自动登录
			$result = dbSelect('user', 'uid, username, email, udertype,picture', 'username="'.$uname.'" and password="'.md5($upass).'"', 'uid desc', 1);

			setcookie('uid',$result[0]['uid'],time()+86400);
			setcookie('username',$result[0]['username'],time()+2592000);
			setcookie('udertype',$result[0]['udertype'],time()+86400);
			setcookie('picture',$result[0]['picture'],time()+86400);
			//setcookie('grade',$result[0]['grade'],time()+86400);
			$uid=$result[0]['uid'];
			$n = "`uid`";
			$v = "$uid";
			$result = dbInsert('profilevisible',$n,$v);
			
			$msg = '<font color=green><b>Thanks for your registration, now you will login as a member</b></font>';
			$url = 'index.php';
			$style = 'alert_right';
			include 'notice.php';

			/*
			$msg = '注册赠送';
			include 'layer.php';
			*/
		}
	
	}else{
		include template("reg.html");
	}

?>
