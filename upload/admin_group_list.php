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

$GroupList = dbSelect('groups','*',null,'name asc');

//delete
if ($_POST['deletesubmitbtn']) {
    $groupId=$_POST['deletesubmitbtn'];
    $result = dbDel('groups', 'gid='.$groupId . '');
    if ($result) {
        $msg = '<font color=red><b>operation succeeded</b></font>';
        $url = 'admin_group_list.php';
        $style = 'alert_right';
        $toTime = 3000;
        include 'notice.php';
        exit;
    } else {
        $msg = '<font color=red><b>operation failed, please contact the administrator</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }
}


$title = 'Admin Center - '.WEB_NAME;
$menu = WEB_NAME;
include template("admin_group_list.html");
