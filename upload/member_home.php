<?php
/**
 * 个人资料
 */

include './common/common.php';
include 'logincheck.php';


$uid = $_GET['uid'];

if ($uid == $_COOKIE['uid']){
    header('location:home.php');
}


$User = dbSelect('user','*','uid='.$uid.'','',1);

if(empty($User)) {
    $msg = '<font color=red><b>such user is not found</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}else{
    $uname = $User[0]['username'];
    $upic = $User[0]['picture'];
    switch ($User[0]['sex']){
        case 1:
            $ugender = 'femal';
            break;
        case 2:
            $ugender = 'male';
            break;
        default:
            $ugender = 'secret';
    }
    $ubirthday = $User[0]['birthday'];
    $uplace = $User[0]['region'];
    $uprofiession = $User[0]['profession'];
    $dateofregistraion = formatTime($User[0]['regtime']);
}

if($User[0]['status']==1){
    $msg = '<font color=red><b>cannot access an inactive user</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}


$Friend = dbSelect('friend','uid,approved,type','uid='.$_COOKIE['uid'].' and fid='.$uid.'','',1);
$friendApp = $Friend[0]['approved'];
$friendType = $Friend[0]['type'];
$Groupmate = dbDuoSelect('gmembers as g1','gmembers as g2','on g1.gid = g2.gid',null,null,'g2.uid as uid','g1.uid ='.$uid.' and g2.uid='.$_COOKIE['uid'].' and g1.approved=1 and g2.approved=1');
$isGroupmate = !empty($Groupmate);

$isadmin = isAdmin();

// profile visibility
$visiblelevel = dbSelect('profilevisible','*','uid='.$uid.'','',1);
$visiblepermission = dbSelect('profilevisiblemember','*','uid='.$uid.' and tid='.$_COOKIE['uid'].'');
$firstnamevisible = ((($friendApp==1 || $isGroupmate)&& $visiblelevel[0]['firstname_visible']==1 && ($visiblepermission[0]['firstname_visible']==1||empty($visiblepermission))) || $visiblelevel[0]['firstname_visible']==2 || $isadmin);
$lastnamevisible = ((($friendApp==1 || $isGroupmate) && $visiblelevel[0]['lastname_visible']==1 && ($visiblepermission[0]['lastname_visible']==1||empty($visiblepermission))) || $visiblelevel[0]['lastname_visible']==2 || $isadmin);
$sexvisible = ((($friendApp==1 || $isGroupmate)&& $visiblelevel[0]['sex_visible']==1 && ($visiblepermission[0]['sex_visible']==1||empty($visiblepermission))) || $visiblelevel[0]['sex_visible']==2 || $isadmin);
$bdayvisible = ((($friendApp==1 || $isGroupmate)&& $visiblelevel[0]['bday_visible']==1 && ($visiblepermission[0]['bday_visible']==1||empty($visiblepermission))) || $visiblelevel[0]['bday_visible']==2 || $isadmin);
$addressvisible = ((($friendApp==1 || $isGroupmate) && $visiblelevel[0]['address_visible']==1 && ($visiblepermission[0]['address_visible']==1||empty($visiblepermission))) || $visiblelevel[0]['address_visible']==2 || $isadmin);
$placevisible = ((($friendApp==1 || $isGroupmate) && $visiblelevel[0]['place_visible']==1 && ($visiblepermission[0]['place_visible']==1||empty($visiblepermission))) || $visiblelevel[0]['place_visible']==2 || $isadmin);
$professionvisible = ((($friendApp==1 || $isGroupmate) && $visiblelevel[0]['profession_visible']==1 && ($visiblepermission[0]['profession_visible']==1||empty($visiblepermission))) || $visiblelevel[0]['profession_visible']==2 || $isadmin);


$FriendRequest = dbSelect('friend','uid,approved,type','fid='.$_COOKIE['uid'].' and uid='.$uid.' and approved=0','',1);

if($_POST['friendsubmitbtn']) {
    if ($friendApp==1){
        $msg = '<font color=red><b>You are already a friend of him/her</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

    if (!empty($Friend) && $friendApp==0){
        $msg = '<font color=red><b>You have already sent a request</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

    $result = dbInsert('friend','uid,fid,type,addtime',''.$_COOKIE['uid'].','.$uid.','.$_POST['type_apply'].','.time().'');
    if(empty($result)){
        $msg = '<font color=red><b>operation failed</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }else{
        header('location:member_home.php?uid='.$uid.'');
    }
}

if($_POST['cancelsubmitbtn']) {
    if (empty($Friend) || $friendApp==0){
        $msg = '<font color=red><b>You are not friends</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

    $result1 = dbDel('friend','uid='.$_COOKIE['uid'].' and fid ='.$uid.'');
    $result2 = dbDel('friend','fid='.$_COOKIE['uid'].' and uid ='.$uid.'');
    if(empty($result1)||empty($result2)){
        $msg = '<font color=red><b>operation failed</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }else{
        header('location:member_home.php?uid='.$uid.'');
    }
}

if($_POST['changesubmitbtn']) {
    if (empty($Friend) || $friendApp==0){
        $msg = '<font color=red><b>You are not friends</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

    if($friendType==$_POST['type_change']){
        header('location:member_home.php?uid='.$uid.'');
    }

    $result = dbUpdate('friend','type='.$_POST['type_change'].'','uid='.$_COOKIE['uid'].' and fid ='.$uid.'');
    if(empty($result)){
        $msg = '<font color=red><b>operation failed</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }else{
        header('location:member_home.php?uid='.$uid.'');
    }
}

$title = 'Member Information - '.WEB_NAME;
include template("member_home.html");

