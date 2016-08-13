<?php
/**
 * group
 */
include './common/common.php';
include 'logincheck.php';

$title = 'Member - ' . WEB_NAME;
$menu = WEB_NAME;

$Mlist = $_GET['mlist'];



$Mlist = $_GET['mlist'];
$Cat = $_GET['cat'];
$FriendRequest = dbSelect('friend','uid,approved,type','fid='.$_COOKIE['uid'].' and approved=0','');

if($Mlist==0){
    switch ($Cat){
        case 1;
            $UserList = dbSelect('user','uid,username,picture, birthday','status!=1','birthday desc');
            break;
        case 2;
            $UserList = dbSelect('user','uid,username,picture, profession','status!=1','profession desc');
            break;
        case 3;
            $UserList = dbSelect('user','uid,username,picture, region','status!=1','region desc');
            break;
        default:
            $UserList = dbSelect('user','uid,username,picture','status!=1','username asc');
            $UserListRest = 8-count($UserList)%8;
            $UserListRest = ($UserListRest ==8)? 0:$UserListRest;
    }
}elseif($Mlist==1){
    switch ($Cat){
        case 1;
            $select='u.uid as uid, u.username as username, u.picture as picture, u.birthday as birthday';
            $UserList = DBduoSelect('user as u','friend as f','on u.uid = f.fid and f.approved=1 and u.status!=1',null,null,$select,'f.uid ='.$_COOKIE['uid'].'','u.birthday desc');
            break;
        case 2;
            $select='u.uid as uid, u.username as username, u.picture as picture, u.profession as profession';
            $UserList = DBduoSelect('user as u','friend as f','on u.uid = f.fid and f.approved=1 and u.status!=1',null,null,$select,'f.uid ='.$_COOKIE['uid'].'','u.profession desc');
            break;
        case 3;
            $select='u.uid as uid, u.username as username, u.picture as picture, u.region as region';
            $UserList = DBduoSelect('user as u','friend as f','on u.uid = f.fid and f.approved=1 and u.status!=1',null,null,$select,'f.uid ='.$_COOKIE['uid'].'','u.region desc');
            break;
        default:
            $select='u.uid as uid, u.username as username, u.picture as picture';
            $FriendList = DBduoSelect('user as u','friend as f','on u.uid = f.fid and f.approved=1 and u.status!=1',null,null,$select,'f.uid ='.$_COOKIE['uid'].' and f.type=0','u.username asc');
            $FriendListRest = 8-count($FriendList)%8;
            $FriendListRest = ($FriendListRest ==8)? 0:$FriendListRest;

            $FamilyList = DBduoSelect('user as u','friend as f','on u.uid = f.fid and f.approved=1 and u.status!=1',null,null,$select,'f.uid ='.$_COOKIE['uid'].' and f.type=1','u.username asc');
            $FamilyListRest = 8-count($FamilyList)%8;
            $FamilyListRest = ($FamilyListRest == 8)? 0:$FamilyListRest;

            $ColleagueList = DBduoSelect('user as u','friend as f','on u.uid = f.fid and f.approved=1 and u.status!=1',null,null,$select,'f.uid ='.$_COOKIE['uid'].' and f.type=2','u.username asc');
            $ColleagueListRest = 8-count($ColleagueList)%8;
            $ColleagueListRest = ($ColleagueListRest == 8)? 0:$ColleagueListRest;
    }
}

include template("member.html");