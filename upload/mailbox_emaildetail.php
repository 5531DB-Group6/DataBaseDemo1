<?php
/**
 * Poat detail
 */

include './common/common.php';


if(empty($_REQUEST['mailid']) )
{
    $msg = '<font color=red><b>Illegal operation is not allowed</b></font>';
    $url = 'mailbox.php';
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
}
$mailid = $_REQUEST['mailid'];

if(!$_COOKIE['uid']){
    $notice='Sorry，you have not logged in';
    include 'close.php';
    exit;
}


dbUpdate('mails','isread = 1','mailid = '.$mailid.'');
$result =dbSelect('mails','*','mailid = '.$mailid.'')[0];
$receiver = dbSelect('user','uid, username','uid= '.$result['receiverid'].'')[0];
$sender = dbSelect('user','uid, username','uid= '.$result['senderid'].'')[0];
$user = dbSelect('user','uid, username','uid= '.$_COOKIE['uid'].'')[0];

//保存帖子回复
if($_POST['replysubmit'])
{

    header('location:mailbox_compose.php?senderid='.$sender['uid']);
}


if($_POST['deletesubmit']){

    dbDel('mails','mailid='.$mailid.'');
    header('location:mailbox.php');

}
$unreadmail = dbSelect('mails','count(*)','receiverid='.$_COOKIE['uid'].' AND isread = 0', 'sendtime desc','');
$unread= $unreadmail[0]['count(*)'];


include template("mailbox_emaildetail.html");
