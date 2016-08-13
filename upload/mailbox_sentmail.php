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


if($_POST['deletesubmit']){
    if(isset($_POST['mailcheckbox'])){
        foreach ($_POST['mailcheckbox'] as $mailid){
            dbDel('mails','mailid='.$mailid.'');
        }
    }
    header('location:mailbox_sentmail.php');
}


$sentmail = dbSelect('mails','*','senderid='.$_COOKIE['uid'].'','sendtime desc','');
$unreadmail = dbSelect('mails','count(*)','receiverid='.$_COOKIE['uid'].' AND isread = 0', 'sendtime desc','');
$unread= $unreadmail[0]['count(*)'];


include template("mailbox_sentmail.html");

