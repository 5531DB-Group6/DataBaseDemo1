<?php


include './common/common.php';


//判断用户是否登录
if(!$_COOKIE['uid'])
{
    $notice = 'Sorry，you are currently not logged in.';
    include 'close.php';
    exit;
}


if($_REQUEST['senderid']){
    $replyid =  $_REQUEST['senderid'];
    $replyto = dbSelect('user','uid, username','uid='.$replyid.'')[0];
}

//send email
if($_POST['mailsubmit'])
{
    $senderid = $_COOKIE['uid'];
    $receivername = strMagic($_POST['sendto']);
    $title = strMagic($_POST['subject']);
    $content = strMagic($_POST['content']);
    $sendtime = time();

    $receivername = preg_replace('/\s+/', '', $receivername);
    $receivers = explode(",", $receivername);
    $receivers =array_unique($receivers);

    foreach ($receivers as $receiver){
        $receiver_id = dbSelect('user','uid,username','username="'.$receiver.'" and status!=1');
        $receiverid = $receiver_id[0]['uid'];
        $receivername = $receiver_id[0]['username'];
//        echo  $receiverid;
//        exit;
        if(!$receiverid){
            $msg = '<font color=red><b>Mail dilivery is failed, please check the username again</b></font>';
            $url = 'mailbox.php';
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }
        else{
            $n = 'senderid, receiverid, title, content, sendtime';
            $v = ''.$senderid.', '.$receiverid.', "'.$title.'", "'.$content.'", '.$sendtime.'';
            $result = dbInsert('mails', $n, $v);
        }
    }

    $msg = '<font color=red><b>Mail deliver succeeded</b></font>';
    $url = 'mailbox.php';
    $style = 'alert_right';
    $toTime = 3000;
    include 'notice.php';

    exit;
}

include template("mailbox_compose.html");

