<?php
/**
 * 个人资料
 */

	include './common/common.php';
    include 'logincheck.php';

	//读取个人资料
	$result = dbSelect('user','*', 'uid='.$_COOKIE['uid'].' and status!=2','',1);
	$visible = dbSelect('profilevisible','*', 'uid='.$_COOKIE['uid'].' ','',1);
	if(!$result)
	{
		$msg = '<font color=red><b>The user does not exist or is banded by the administrator</b></font>';
		$url = $_SERVER['HTTP_REFERER'];
		$style = 'alert_error';
		$toTime = 3000;
		include 'notice.php';
		exit;
	}

    $Jg = $result[0]['region'];
    if(!empty($result[0]['birthday']))
    {
        $bArr = explode('-', $result[0]['birthday']);
        $yBirthday = $bArr[0];
        $mBirthday = $bArr[1];
        $dBirthday = $bArr[2];
    }else{
        $yBirthday = '';
        $mBirthday = '';
        $dBirthday = '';
    }

    $yArr = [];
    $yn = date('Y');
    for($i=0; $i<100; $i++)
    {
        $yArr[] = $yn - $i;
    }

    $mArr= [];
    $mn = 1;
    for($i=0; $i<12; $i++)
    {
        $mArr[] = $mn + $i;
    }

    $dArr = [];
    $dn = 1;
    for($i=0; $i<30; $i++)
    {
        $dArr[] = $dn+$i;
    }


    //修改个人资料
    if($_POST['profilesubmitbtn'])
    {

        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $sex = $_POST['sex'];
        $birth = $_POST['birthyear'].'-'.$_POST['birthmonth'].'-'.$_POST['birthday'];
        $address = $_POST['address'];
        $place = $_POST['place'];
        $profession = $_POST['profession'];

        $firstname_visible = $_POST['firstname_visible'];
        $lastname_visible = $_POST['lastname_visible'];
        $sex_visible = $_POST['sex_visible'];
        $bday_visible = $_POST['birthday_visible'];
        $address_visible = $_POST['address_visible'];
        $place_visible = $_POST['place_visible'];
        $profession_visible = $_POST['profession_visible'];

        $owner = dbUpdate('user', 'firstname="'.$firstname.'",lastname="'.$lastname.'",sex='.$sex.',birthday="'.$birth.'",address="'.$address.'",region="'.$place.'",profession="'.$profession.'" ', 'uid='.$_COOKIE['uid'].'');
        $visible= dbUpdate('profilevisible', 'firstname_visible='.$firstname_visible.',lastname_visible='.$lastname_visible.',sex_visible='.$sex_visible.',bday_visible='.$bday_visible.',address_visible='.$address_visible.',place_visible='.$place_visible.',profession_visible='.$profession_visible.'','uid='.$_COOKIE['uid'].'');
        //$owner = dbUpdate('user', 'firstname="'.$firstname.'",lastname="'.$lastname.'",sex='.$sex.',birthday="'.$birth.'",region="'.$place.'"', 'uid='.$_COOKIE['uid'].'');
        //$owner = dbUpdate('user', 'firstname="'.$firstname.'",lastname="'.$lastname.'",sex='.$sex.',region="'.$place.'"', 'uid='.$_COOKIE['uid'].'');
        if($owner || $visible)
        {
            header('location:home.php');
        }else{
            $msg = '<font color=red><b>Error，please contact the administrator</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }
    }


	$title = 'Personal Information - '.WEB_NAME;
	include template("home.html");

