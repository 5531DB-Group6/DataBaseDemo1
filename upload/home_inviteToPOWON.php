<?php


include './common/common.php';

include 'logincheck.php';



if ($_POST['invitesubmitbtn']){
    $email = $_POST['emailaddress'];

    if($email){
        $user = dbSelect('user','uid,username,email,firstname','uid='.$_COOKIE['uid'].'')[0];

        $to = "$email";
        $from = $user['email'];
        $firstname = $user['firstname'];
        $from = "chao.wang86@yahoo.ca";
        $to = "vampire.8690@hotmail.com";
        $subject = 'Social Net Account Activation';
        $message ='<!DOCTYPE html><html><head><meta charset="UTF-8"><title>POWON Message</title></head><body style="margin:0px; font-family:Tahoma, Geneva, sans-serif;"><div style="padding:10px; background:#333; font-size:24px; color:#CCC;"><a href="http://localhost/Snafo_POWON/POWON/index.php"><img src="" width="36" height="30" alt="yoursitename" style="border:none; float:left;"></a>POWON Account Activation</div><div style="padding:24px; font-size:17px;">Hello ,<br /><br />My first name is : '.$firstname.'<br /><br /><a href="http://localhost/Snafo_POWON/POWON/reg.php">Click here to go to our webpage register with my firstname</a><br /><br />Login after successful activation using your:<br />* username</div></body></html>';
        $headers = "From: $from\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\n";

        $result = mail($to, $subject, $message, $headers);
        if($result){
            $msg = '<font color=red><b>'.$type.'invitation is sent</b></font>';
            $url = 'home.php';
            $style = 'alert_right';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }

        header('location:home_inviteToPOWON.php');
    }else{
        $msg = '<font color=red><b>'.$type.'operation failed</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }
}
include template("home_inviteToPOWON.html");
