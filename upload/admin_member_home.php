<?php
/**
 * 个人资料
 */

include './common/common.php';
include 'logincheck.php';


$isadmin = isAdmin();
if (!$isadmin){
    $msg = '<font color=red><b>You are not an administrator</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}

$uid=$_REQUEST['uid'];

//读取个人资料
$result = dbSelect('user','*', 'uid='.$uid.'','',1);
if(!$result)
{
    $msg = '<font color=red><b>The user does not exist</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}

$Jg = $result[0]['region'];
$uname = $result[0]['username'];
$upic = $result[0]['picture'];
switch ($result[0]['sex']){
    case 1:
        $ugender = 'femal';
        break;
    case 2:
        $ugender = 'male';
        break;
    default:
        $ugender = 'secret';
}
$ubirthday = $result[0]['birthday'];
$uplace = $result[0]['region'];
$uprofiession = $result[0]['profession'];
$dateofregistraion = formatTime($result[0]['regtime']);


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
if($_POST['adminprofilesubmitbtn'])
{

    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $sex = $_POST['sex'];
    $birth = $_POST['birthyear'].'-'.$_POST['birthmonth'].'-'.$_POST['birthday'];
    $address = $_POST['address'];
    $place = $_POST['place'];
    $profession = $_POST['profession'];
    $uid=$_POST['uid'];

    $owner = dbUpdate('user', 'firstname="'.$firstname.'",lastname="'.$lastname.'",sex='.$sex.',birthday="'.$birth.'",address="'.$address.'",region="'.$place.'",profession="'.$profession.'" ', 'uid='.$uid.'');

    if($owner)
    {
        header('location:admin_member_list.php');
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
include template("admin_member_home.html");

