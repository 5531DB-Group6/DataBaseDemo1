<?php


include './common/common.php';

include 'logincheck.php';

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
}

$picture = htmlspecialchars("<img src=\"public/images/treasure-chest.gif\" alt=\"treasure chest\" style=\"width:480px;height:480px;\">");

$primarymail = dbSelect('mails','*','receiverid='.$_COOKIE['uid'].'','sendtime desc','');
$socialmail = dbSelect('mails','*','receiverid='.$_COOKIE['uid'].' AND senderid <> 1 AND content<>"public/images/treasure-chest.gif"', 'sendtime desc','');
$giftmail = dbSelect('mails','*','receiverid='.$_COOKIE['uid'].' AND content="public/images/treasure-chest.gif" ','sendtime desc','');
$unreadmail = dbSelect('mails','count(*)','receiverid='.$_COOKIE['uid'].' AND isread = 0', 'sendtime desc','');
$unread= $unreadmail[0]['count(*)'];

include template("mailbox.html");

