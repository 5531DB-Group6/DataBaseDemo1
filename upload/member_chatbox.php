<?php
/**
 * Created by PhpStorm.
 * User: Chao
 * Date: 31/07/2016
 * Time: 5:05 PM
 */
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


if($_POST['msgsubmitbtn']) {
    $msg = $_POST['msg'];
    if($msg!=''){
        $result = dbInsert('chat','uid,fid,msg,posttime', ''.$_COOKIE['uid'].','.$uid.',"'.$msg.'",'.time().'');
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);

}
//
//    $result = dbInsert('chat','uid,fid,msg,posttime', ''.$_COOKIE['uid'].','.$uid.',"'.$msg.'",'.time().'');
//
//    $history = dbSelect('chat','*','(uid='.$_COOKIE['uid'].' AND fid='.$uid.') OR (fid='.$_COOKIE['uid'].' AND uid='.$uid.')','posttime desc','');
//
//    $sender = dbSelect('user','uid,username,picture','uid='.$_COOKIE['uid'].'','','');
//    $receiver = dbSelect('user','uid,username,picture','uid='.$uid.'','','');
//
//
//if($history){
//        foreach ($history as $row){
//            echo "<span class='uid'>".$row['uid']."</span>: <span class='msg'>".$row['msg']."</span></br>";
//            echo gmdate("Y-m-d\TH:i:s\Z ", $row['posttime'])."</br>";
//        }
//    }
//    else{
//        echo "Loading chatlogs please wait...";
//    }
//


