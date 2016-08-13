<?php
/**
 * group post list
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
$linum = 10;

if ($_POST['newpostsubmitbtn']){
    header('location:admin_addc.php');
}

//读取版块内帖子信息
$ListContent = dbSelect('pposts','*','isdel=0','pid desc');

$title = $OnGname.' - '.WEB_NAME;
$menu = WEB_NAME;
include template("admin_postlist.html");
