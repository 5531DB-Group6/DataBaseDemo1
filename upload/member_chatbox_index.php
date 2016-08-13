<?php
include './common/common.php';


if(empty($_COOKIE['uid']))
{
    $msg = '<font color=red><b>You have not logged in</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}
$uid = $_GET['uid'];

if ($uid == $_COOKIE['uid']){
    header('location:group_postlist.php');

}

dbUpdate('chat','isread=1','uid='.$uid.' and fid='.$_COOKIE['uid'].'');


$sender = dbSelect('user','uid,username,picture','uid='.$_COOKIE['uid'].'','','');
$receiver = dbSelect('user','uid,username,picture','uid='.$uid.'','','');

$sender=array_values($sender)[0];
$receiver=array_values($receiver)[0];

$history = dbSelect('chat','*','(uid='.$_COOKIE['uid'].' AND fid='.$uid.') OR (fid='.$_COOKIE['uid'].' AND uid='.$uid.')','posttime desc','');


include template("member_chatbox_index.html");