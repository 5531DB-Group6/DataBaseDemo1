<?php
/**
 * group post list
 */
include './common/common.php';
include 'logincheck.php';


$uid = $_GET['uid'];

if ($uid == $_COOKIE['uid']){
    header('location:home_postlist.php');
}

$Friend = dbSelect('friend','uid,approved,type','uid='.$_COOKIE['uid'].' and fid='.$uid.'','',1);
$friendApp = $Friend[0]['approved'];
if (!empty($Friend) && $friendApp==0){
    $msg = '<font color=red><b>You are not a friend of him/her</b></font>';
    $url = 'member_home?uid='.$uid.'';
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}

$statuscheck = dbSelect('user','status','uid='.$uid.'');
if($statuscheck[0]['status']==1){
    $msg = '<font color=red><b>cannot access an inactive user</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}

//该版块下的主题数量
$TZCount = dbFuncSelect('uposts','count(pid)','first=1 and isdel=0 and authorid='.$uid.'');
$zCount = $TZCount['count(pid)'];

$linum = 10;	//每页显示数量

//读取版块内帖子信息
$ListContent = dbSelect('uposts','pid,title,authorid,addtime,replycount,hits','first=1 and isdel=0 and authorid='.$uid.'','pid desc', setLimit($linum));

//该板块下今日主题数量
$newt = time()-1000;
$start_time = strtotime(date('Y-m-d',time()));
$JRCount = dbFuncSelect('gposts','count(pid)','first=1 and isdel=0 and (addtime>='.$start_time.' and addtime<='.time().')');
$JCount = $JRCount['count(pid)'];

$title = 'Personal Post List - '.WEB_NAME;
$menu = WEB_NAME;
include template("member_postlist.html");