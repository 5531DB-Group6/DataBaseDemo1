<?php
/**
 * group
 */
include './common/common.php';
include 'logincheck.php';

$title = 'Group - ' . WEB_NAME;
$menu = WEB_NAME;

$Glist = $_GET['glist'];
$Cat = $_GET['cat'];

//判断用户是否登录
if(!$_COOKIE['uid'])
{
    $notice = 'Sorry, you have not logged in，';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}

if($Glist==0 && $Cat==0) {
    $GrMenu = dbSelect('groups', 'gid,name,owner,grouppic, description', null, 'name asc');
    //$sql = 'select distinct description from '.DB_PREFIX.' groups group by description';
}elseif($Glist==0 && $Cat==1){
    $GrMenu = dbSelect('groups', 'gid,name,owner,grouppic, description', null, 'description desc');
}elseif($Glist==1 && $Cat==0){
    $select='g.gid as gid, g.name as name, g.grouppic as grouppic,g.owner as owner, g.description as description';
    $GrMenu = DBduoSelect('groups as g','gmembers as m','on g.gid = m.gid and m.approved=1',null,null,$select,'m.uid ='.$_COOKIE['uid'].'');
}elseif($Glist==1 && $Cat==1){
    $select='g.gid as gid, g.name as name, g.grouppic as grouppic,g.owner as owner, g.description as description';
    $GrMenu = DBduoSelect('groups as g','gmembers as m','on g.gid = m.gid and m.approved=1',null,null,$select,'m.uid ='.$_COOKIE['uid'].'','description desc');
}


/*
if(!empty( $_GET['ap'])){
    $result=dbInsert('gmembers','gid,uid',''.$groupId.','.$_COOKIE['uid'].'');
    if($resutl){
        $notice = 'apply submitted,';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_right';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }else{
        $notice = 'apply failed';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_right';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }
}
*/


include template("group.html");