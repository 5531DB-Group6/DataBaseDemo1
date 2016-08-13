<?php
/**
 * group member list
 */
include './common/common.php';
include 'logincheck.php';

$isadmin = isAdmin();
if (!$isadmin){
    $msg = '<font color=red><b>You are not an administrator</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}



$linum = 10;	//每页显示数量


$MemberList = dbSelect('user','*',null,'username asc');


    //status change
    if($_POST['statussubmitbtn']){
        $targetId = $_POST['statussubmitbtn'];
        $Target = dbSelect('user','*','uid='.$targetId.'','',1);

        $Tuid = $Target[0]['uid'];
        $Tadmin = $Target[0]['udertype'];
        if ($Target[0]['udertype']==1){
            $msg = '<font color=red><b>target user is an admin</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }

        $status =$_POST['status'.$Tuid];
        $result = dbUpdate('user','status='.$status.'','uid='.$Tuid.'');
        if ($result){
            header('location:admin_member_list.php');
        }else{
            $msg = '<font color=red><b>operation failed</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }
    }

    if($_POST['upgradesubmitbtn']){
        $targetId = $_POST['upgradesubmitbtn'];
        $Target = dbSelect('user','*','uid='.$targetId.'','',1);

        $Tuid = $Target[0]['uid'];
        $Tadmin = $Target[0]['udertype'];
        if ($Target[0]['udertype']==1){
            $msg = '<font color=red><b>target user is an admin</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }

        $result = dbUpdate('user','udertype=1, status=0','uid='.$Tuid.'');
        if ($result){
            $msg = '<font color=red><b>operation succeeded</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_right';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }else{
            $msg = '<font color=red><b>operation failed</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }
    }
    //delete
    if($_POST['deletesubmitbtn']){
        $targetId = $_POST['deletesubmitbtn'];
        $Target = dbSelect('user','*','uid='.$targetId.'','',1);

        $Tuid = $Target[0]['uid'];
        $Tadmin = $Target[0]['udertype'];
        if ($Target[0]['udertype']==1){
            $msg = '<font color=red><b>target user is an admin</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }

        $result = dbDel('user','uid='.$Tuid.'');
        if ($result){
            $msg = '<font color=red><b>operation succeeded</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_right';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }else{
            $msg = '<font color=red><b>operation failed</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }
    }


$title = 'Admin Center - '.WEB_NAME;
$menu = WEB_NAME;
include template("admin_member_list.html");
