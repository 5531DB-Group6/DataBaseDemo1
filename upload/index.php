<?php
/**
 * Home
 */
include './common/common.php';

$title = 'Home - ' . WEB_NAME;
$menu = WEB_NAME;

$publicpost = dbSelect('pposts','*','','pid desc');

//echo ($_COOKIE['username'] =="");
//判断用户是否登录

if( $_COOKIE['uid'] )
{

    $UserList = dbSelect('user','uid,username,picture','status!=1','username asc');
    $UserListRest = 8-count($UserList)%8;
    $UserListRest = ($UserListRest ==8)? 0:$UserListRest;

    $select='u.uid as uid, u.username as username, u.picture as picture';
    $FriendList = DBduoSelect('user as u','friend as f','on u.uid = f.fid and f.approved=1',null,null,$select,'f.uid ='.$_COOKIE['uid'].' and u.status!=1','u.username asc');
    $FriendListRest = 8-count($FriendList)%8;
    $FriendListRest = ($FriendListRest ==8)? 0:$FriendListRest;

    $FriendRequest = dbSelect('friend','uid,approved,type','fid='.$_COOKIE['uid'].' and approved=0','');

    $GrMenuAll = dbSelect('groups','gid,name,owner, grouppic',null,'name asc');

    $select='g.gid as gid, g.name as name, g.grouppic as grouppic,g.owner as owner';
    $GrMenu = DBduoSelect('groups as g','gmembers as m','on g.gid = m.gid and m.approved=1',null,null,$select,'m.uid ='.$_COOKIE['uid'].'','g.name asc');

}

//读取帖子总数
$motifCount = dbFuncSelect('category','sum(motifcount)','parentid<>0');
$tzCount = $motifCount['sum(motifcount)'];

//会员数量
$userCount = dbFuncSelect('user','count(uid)');
$userC = $userCount['count(uid)'];

//最新加入会员
$newUser = dbSelect('user','username','','uid desc',1);
$uName = $newUser ? $newUser[0]['username'] : 'none';

//读取图片友情链接
//$imgUrl = dbSelect('link','*','description!="" or logo!=""','displayorder desc,lid desc');

//读取文字友情链接
//$textUrl = dbSelect('link','*','logo<=>null or logo=""','displayorder desc,lid desc');

include template("index.html");