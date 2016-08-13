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
if($_POST['giftsubmit'])
{
    $senderid = $_COOKIE['uid'];
    $receivername = strMagic($_POST['sendto']);
    $title = strMagic($_POST['subject']);
    $sendto = strMagic($_POST['sendto']);
    $content = "public/images/treasure-chest.gif";
    $sendtime = time();

    $sender = dbSelect('user','uid,username,coins','uid="'.$_COOKIE['uid'].'"')[0];
    $coins = $sender['coins'];
    $receivername = preg_replace('/\s+/', '', $receivername);
    $receivers = explode(",", $receivername);
    $receivers =array_unique($receivers);

    foreach ($receivers as $receiver){
        $eachreceiver = dbSelect('user','uid,username,coins','username="'.$receiver.'"')[0];
        $receiverid = $eachreceiver['uid'];
        $receivername = $eachreceiver['username'];

        if(!$receiverid){
            $msg = '<font color=red><b>Mail dilivery is failed, please check the username again</b></font>';
            $url = 'mailbox.php';
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }
        else{
            if($coins <= 0){
                $msg = '<font color=red><b>You do not have enough coins</b></font>';
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
                $coins = $coins -1;
                dbUpdate('user','coins='.$coins.'','uid='.$sender['uid'].'');
                $latestreceiver = dbSelect('user','uid,username,coins','username="'.$receiver.'"')[0];
                dbUpdate('user','coins='.$latestreceiver['coins'].'+1','uid='.$receiverid.'');

            }
        }
    }

    $msg = '<font color=red><b>Gift deliver succeeded</b></font>';
    $url = 'mailbox.php';
    $style = 'alert_right';
    $toTime = 3000;
    include 'notice.php';

    exit;
}

include template("mailbox_sendgift.html");

