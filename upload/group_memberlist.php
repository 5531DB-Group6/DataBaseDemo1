<?php
/**
 * group member list
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

$result = dbSelect('gmembers','uid,approved,admin,mute','uid='.$_COOKIE['uid'].' and gid='.$groupId.'','',1);
$approved = $result[0]['approved'];
$isadmin = isAdmin();
$result = dbSelect('groups','owner','gid='.$groupId.'','',1);
$owner = $_COOKIE['uid']==(int)$result[0]['owner'];
if($isadmin||$owner){
    $admin=1;
}else{
    $admin=0;
}

if((!$result || $approved==0)&&!$admin)
{
    $msg = '<font color=red><b>You are not a member of the group<br>please apply for admission</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}

//读取导航索引
/*
$classId = 1;
$category = dbSelect('category','cid,classname,parentid','parentid<>0 and cid='.$classId.'','',1);
if($category)
{
    $smallName = $category[0]['classname'];
    $smallId = $category[0]['cid'];
    $parentCategory = dbSelect('category','cid,classname','cid='.$category[0]['parentid'].'','',1);
    if($parentCategory)
    {
        $bigName = $parentCategory[0]['classname'];
        $bigId = $parentCategory[0]['cid'];
    }else{
        $msg = '<font color=red><b>非法操作</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style='alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

}else{

    $msg = '<font color=red><b>非法操作</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}
*/



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

$OwnerId = (int)$Owner;

//读取所有大版块信息
//$LTmenu = dbSelect('category','cid,classname','parentid=0 and ispass=1','orderby desc,cid desc');
//读取所有小版块信息
//$LTsMenu = dbSelect('category','cid,classname,parentid','parentid<>0 and ispass=1','orderby desc,cid desc');


//number of members
$TZCount = dbFuncSelect('gmembers','count(uid)','gid='.$groupId.' and approved=1');
$zCount = $TZCount['count(uid)'];

$linum = 10;	//每页显示数量

//Read group member info
//$ListContent = dbSelect('gposts','uid,title,authorid,addtime,replycount,hits,style','first=1 and isdel=0 and gid='.$groupId.'','pid desc', setLimit($linum));
$select='u.uid as uid, u.username as username,u.picture as picture,m.admin as admin, m.mute as mute';
//$MemberList = DBduoSelect('groups as g','gmembers as m','on g.gid = m.gid','user as u','on u.uid = m.uid',$select,'g.gid ='.$groupId.'');
$MemberList = DBduoSelect('user as u','gmembers as m','on u.uid = m.uid and m.approved=1 and status!=1',null,null,$select,'m.gid ='.$groupId.'');
//$PendingList = DBduoSelect('user as u','gmembers as m','on u.uid = m.uid and m.approved=0',null,null,$select,'m.gid ='.$groupId.'');

if($admin){
    //kick out member
    if(!empty($_GET['del'])){
        $targetId = $_GET['uid'];
        $Target = dbSelect('gmembers','gid,uid,admin','gid='.$groupId.' and uid='.$targetId.'','',1);

        if (!$Target) {
            $msg = '<font color=red><b>target user is not a member of the group</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }

        $Tuid = $Target[0]['uid'];
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
    //mute
    if(!empty($_GET['mut'])){
        $targetId = $_GET['uid'];
        $Target = dbSelect('gmembers','gid,uid,admin,mute','gid='.$groupId.' and uid='.$targetId.'','',1);

        if (!$Target) {
            $msg = '<font color=red><b>target user is not a member of the group</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }

        $Tuid = $Target[0]['uid'];
        $Tmute = $Target[0]['mute'];

        $result = dbUpdate('gmembers', 'mute=1','uid='.$Tuid.' and gid='.$groupId.'');
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

    if(!empty($_GET['unm'])){
        $targetId = $_GET['uid'];
        $Target = dbSelect('gmembers','gid,uid,admin,mute','gid='.$groupId.' and uid='.$targetId.'','',1);

        if (!$Target) {
            $msg = '<font color=red><b>target user is not a member of the group</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }

        $Tuid = $Target[0]['uid'];
        $Tmute = $Target[0]['mute'];

        $result = dbUpdate('gmembers', 'mute=0','uid='.$Tuid.' and gid='.$groupId.'');
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
include template("group_memberlist.html");
