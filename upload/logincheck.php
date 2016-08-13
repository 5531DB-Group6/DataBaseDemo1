<?php
if(empty($_COOKIE['uid'])&&empty($_COOKIE['username']))
{
    $msg = '<font color=red><b>You have not logged in</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}

$statuscheck = dbSelect('user','status','uid='.$_COOKIE['uid'].'');
if($statuscheck[0]['status']==2)
{
    $msg = '<font color=red><b>your have been suspended by the administrator</b></font>';
    $url = 'logout.php';
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}