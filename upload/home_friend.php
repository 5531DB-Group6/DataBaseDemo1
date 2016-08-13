<?php
/**
 * 个人资料
 */

include './common/common.php';
include 'logincheck.php';


$select='u.uid as uid, u.username as username, u.picture as picture';
$Friend = dbDuoSelect('friend as f','user as u','on u.uid=f.uid and f.approved=0',null,null,$select,'f.fid='.$_COOKIE['uid'].'','f.addtime desc');

if ($_POST['approvesubmitbtn']){
    $uid=$_POST['approvesubmitbtn'];
    $type = $_POST['type_'.$uid.''];

    $result = dbupdate('friend', 'approved=1','uid='.$uid.' and fid='.$_COOKIE['uid'].'');
    $insert = dbInsert('friend','uid,fid,approved,type,addtime',''.$_COOKIE['uid'].','.$uid.',1,'.$type.','.time().'');

    if($result && $insert){

        $uposts = dbselect('uposts','pid','first=1 and isdel=0 and authorid='.$uid.'');
        foreach($uposts as $key=>$val) {
            dbInsert('upostspermission', 'pid, uid', '' . $val['pid'] . ',' . $_COOKIE['uid'] . '');
        }

        $uposts = dbselect('uposts','pid','first=1 and isdel=0 and authorid='.$_COOKIE['uid'].'');
        foreach($uposts as $key=>$val) {
            dbInsert('upostspermission', 'pid, uid', '' . $val['pid'] . ',' . $uid . '');
        }

        header('location:home_friend.php');
    }else{
        $msg = '<font color=red><b>'.$type.'operation failed</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }
}

if ($_POST['rejectsubmitbtn']){
    $uid=$_POST['rejectsubmitbtn'];

    $result = dbDel('friend','uid='.$uid.' and fid='.$_COOKIE['uid'].' and approved=0');

    if($result){
        header('location:home_friend.php');
    }else{
        $msg = '<font color=red><b>'.$type.'operation failed</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }
}


$title = '个人资料 - '.WEB_NAME;
include template("home_friend.html");

