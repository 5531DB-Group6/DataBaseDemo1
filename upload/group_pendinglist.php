<?php
/**
 * group pending list
 */
include './common/common.php';
include 'logincheck.php';


//判断ID是否存在
if(empty($_GET['gid']) || !is_numeric($_GET['gid']))
{
    $msg = '<font color=red><b>Illegal operation is not allowed</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}else{
    $groupId = $_GET['gid'];
}

$isadmin = isAdmin();
$result = dbSelect('groups','owner','gid='.$groupId.'','',1);
$owner = $_COOKIE['uid']==(int)$result[0]['owner'];

if($isadmin||$owner){
    $admin=1;
}else{
    $admin=0;
}


if(!$result || $admin==0)
{
    $msg = '<font color=red><b>You are not an admin of the group</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}


$OnMenu = dbSelect('groups','gid,name,owner','gid='.$groupId.' and ispass=1','orderby desc,gid desc');
if(!$OnMenu)
{
    $msg = '<font color=red><b>cannot find the group</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
}else{
    $OnGid = $OnMenu[0]['gid'];
    $OnGname = $OnMenu[0]['name'];
    $Owner = $OnMenu[0]['owner'];
}

//number of members
$TZCount = dbFuncSelect('gmembers','count(uid)','gid='.$groupId.' and approved=1');
$zCount = $TZCount['count(uid)'];

$linum = 10;	//每页显示数量


$select='u.uid as uid, u.username as username,u.picture as picture,m.admin as admin';
//$MemberList = DBduoSelect('user as u','gmembers as m','on u.uid = m.uid and m.approved=1',null,null,$select,'m.gid ='.$groupId.'');
$PendingList = DBduoSelect('user as u','gmembers as m','on u.uid = m.uid and m.approved=0',null,null,$select,'m.gid ='.$groupId.'');

if($admin){
    //decline application
    if(!empty($_GET['del'])){
        $targetId = $_GET['uid'];
        $Target = dbSelect('gmembers','gid,uid,approved','gid='.$groupId.' and uid='.$targetId.'','',1);

        if (!$Target) {
            $msg = '<font color=red><b>such user cannot be found</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }

        $Tuid = $Target[0]['uid'];
        $Tadmin = $Target[0]['approved'];
        if ($Target[0]['approved']==1){
            $msg = '<font color=red><b>target user is already a member of the group</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }

        $result = dbDel('gmembers', 'uid='.$Tuid.' and gid='.$groupId.'');
        if ($result){
            $msg = '<font color=red><b>operation succeeded</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_right';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }else{
            $msg = '<font color=red><b>operation failed, please contact the administrator</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }
    }
    //approve application
    if(!empty($_GET['app'])){
        $targetId = $_GET['uid'];
        $Target = dbSelect('gmembers','gid,uid,approved','gid='.$groupId.' and uid='.$targetId.'','',1);

        if (!$Target) {
            $msg = '<font color=red><b>such user cannot be found</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }

        $Tuid = $Target[0]['uid'];
        $Tadmin = $Target[0]['approved'];
        if ($Target[0]['approved']==1){
            $msg = '<font color=red><b>target is already a member of the group</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }

        $result = dbUpdate('gmembers', 'approved=1','uid='.$Tuid.' and gid='.$groupId.'');
        if ($result){
            $gposts = dbselect('gposts','pid','first=1 and isdel=0 and gid='.$groupId.'');
            foreach($gposts as $key=>$val){
                dbInsert('gpostspermission','pid, uid',''.$val['pid'].','.$Tuid.'');
            }

            $msg = '<font color=red><b>operation succeeded</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_right';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }else{
            $msg = '<font color=red><b>operation failed, please contact the administrator</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }
    }
}



/*
//该板块下今日主题数量
$newt = time()-1000;
$start_time = strtotime(date('Y-m-d',time()));
$JRCount = dbFuncSelect('gposts','count(pid)','first=1 and isdel=0 and (addtime>='.$start_time.' and addtime<='.time().')');
$JCount = $JRCount['count(pid)'];
*/

$title = $OnGname.' - '.WEB_NAME;
$menu = WEB_NAME;
include template("group_pendinglist.html");
