<?php
/**
 * Add posts
 */

include './common/common.php';
include 'logincheck.php';

//判断ID是否存在

//判断帖子ID是否存在
if(empty($_REQUEST['pid']) || !is_numeric($_REQUEST['pid']))
{
    $msg = '<font color=red><b>Illegal operation is not allowed</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
}
$Id=$_REQUEST['pid'];
$groupcheck = dbSelect('gposts','gid, title','pid='.$Id.'');
$ptitle = $groupcheck[0]['title'];
$groupId = $groupcheck[0]['gid'];
$OnMenu = dbSelect('groups','gid,name,owner','gid='.$groupId.'','gid desc');
$OnGid = $OnMenu[0]['gid'];
$OnGname = $OnMenu[0]['name'];
$Owner = $OnMenu[0]['owner'];
$isOwner = $_COOKIE['uid'] == (int)$OnMenu[0]['owner'];

$isadmin = isAdmin() || $isOwner;
if(!$isadmin){
    $msg = '<font color=red><b>You are not allowed to modify the post permission</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}


$select='u.uid as uid, u.username as username,u.picture as picture';
$MemberList = DBduoSelect('user as u','gmembers as m','on u.uid = m.uid and m.approved=1',null,null,$select,'m.gid ='.$groupId.'');
$MemberListRest = 3-count($MemberList)%3;
$MemberListRest = ($MemberListRest ==3)? 0:$MemberListRest;

//发布帖子
if($_POST['permissionsubmit'])
{
    if (is_array($MemberList)) {
        foreach ($MemberList AS $key => $val) {
            $uid = $val['uid'];
            $Id = $_POST['pid'];
            $permission = $_POST['permission' . $uid . ''];
            switch ($permission) {
                case 0:
                    $view = 0;
                    $comment = 0;
                    $addlink = 0;
                    break;
                case 1:
                    $view = 1;
                    $comment = 0;
                    $addlink = 0;
                    break;
                case 2:
                    $view = 1;
                    $comment = 1;
                    $addlink = 0;
                    break;
                case 3:
                    $view = 1;
                    $comment = 1;
                    $addlink = 1;
                    break;
            }
            $postpermissionempty = $_POST['postpermission' . $val['uid']];

            if (!$postpermissionempty) {
                $presult = dbUpdate('gpostspermission', 'view=' . $view . ', comment=' . $comment . ', addlink=' . $addlink . '', 'uid=' . $uid . ' and pid=' . $Id . '');
            } else {
                $presult = dbInsert('gpostspermission', 'uid,pid,view,comment,addlink', '' . $uid . ',' . $Id . ',' . $view . ',' . $comment . ',' . $addlink . '');
            }
        }
    }
}


$title = 'Permission modification'.$OnCname.' - '.WEB_NAME;
$menu = WEB_NAME;
include template("group_permission_modify.html");

