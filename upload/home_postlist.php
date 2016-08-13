<?php
/**
 * group post list
 */
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

$uid = $_COOKIE['uid'];

//读取所有大版块信息
$LTmenu = dbSelect('category','cid,classname','parentid=0 and ispass=1','orderby desc,cid desc');
//读取所有小版块信息
$LTsMenu = dbSelect('category','cid,classname,parentid','parentid<>0 and ispass=1','orderby desc,cid desc');


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

if ($_POST['newpostsubmitbtn']){
    header('location:home_addc.php');
}

$title = 'Personal Post List - '.WEB_NAME;
$menu = WEB_NAME;
include template("home_postlist.html");
