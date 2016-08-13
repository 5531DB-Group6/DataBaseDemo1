<?php
/**
 * 个人资料，用户签名
 */

include './common/common.php';
include 'logincheck.php';

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

$OnMenu = dbSelect('groups','gid,name,owner,grouppic,description','gid='.$groupId.' and ispass=1','orderby desc,gid desc');
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
    $Gpic=$OnMenu[0]['grouppic'];
    $description = $OnMenu[0]['description'];
    $isOwner = ($Owner == $_COOKIE['uid'])? 1:0;
}

$result = dbSelect('gmembers','uid,approved,admin','uid='.$_COOKIE['uid'].' and gid='.$groupId.'','',1);
$approved = $result[0]['approved'];
$isadmin = isAdmin();

if($isadmin||$isOwner){
    $admin=1;
}else{
    $admin=0;
}

/*
if(!$result)
{
    $msg = '<font color=red><b>You are not a member of the group</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}
*/



if ($admin) {
    //Change the logo and the description of the group
    if($_POST['profilesubmitbtn'])
    {
        if(!empty($_FILES['pic']['name']))
        {
            $picture = upload('pic');
            $description = $_POST['groupdescription'];
            $owner = dbUpdate('groups', 'grouppic="'.$picture.'", description="'.$description.'"', 'gid='.$groupId.'');
            if($owner){
                header('location:group_info.php?gid='.$groupId.'');
            }else{
                $msg = '<font color=red><b>error，please contact the admin</b></font>';
                $url = $_SERVER['HTTP_REFERER'];
                $style = 'alert_error';
                $toTime = 3000;
                include 'notice.php';
                exit;
            }
        }else{
            $description = $_POST['groupdescription'];
            $owner = dbUpdate('groups', 'description="'.$description.'"', 'gid='.$groupId.'');
            header('location:group_info.php?gid='.$groupId.'');
        }
    }

    //invite a POWON member
    if ($_POST['invitesubmitbtn']) {
        if(empty($_POST['username'])||empty($_POST['useremail'])){
            $msg = '<font color=red><b>please enter the valid username and email address</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }

        $username = strMagic($_POST['username']);
        $email = $_POST['useremail'];
        $userfirstname = $_POST['userfirstname'];
        $birth = $_POST['birthyear'].'-'.$_POST['birthmonth'].'-'.$_POST['birthday'];

        $usercheck = dbSelect('user','uid','username="'.$username.'" and email="'.$email.'" and birthday="'.$birth.'" and firstname="'.$userfirstname.'"');
        if(!$usercheck){
            $msg = '<font color=red><b>no such user is found</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }


        $select = 'u.uid as uid, u.username as username,u.picture as picture,m.approved as approved';
        $MemberList = DBduoSelect('user as u', 'gmembers as m', 'on u.uid = m.uid', null, null, $select, 'm.gid =' . $groupId . ' and u.username="' . $username . '"');

        if ($MemberList) {
            if ($MemberList[0]['approved'] == 1) {
                $msg = '<font color=red><b>the user is already a member of the group</b></font>';
                $url = $_SERVER['HTTP_REFERER'];
                $style = 'alert_error';
                $toTime = 3000;
                include 'notice.php';
                exit;
            } else {
                $result = dbUpdate('gmembers', 'approved=1', 'uid=' . $MemberList[0]['uid'] . ' and gid=' . $groupId . '');
                if ($result) {
                    $msg = '<font color=red><b>operation succeeded</b></font>';
                    $url = $_SERVER['HTTP_REFERER'];
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

        }

        $Target = dbSelect('user', 'uid', 'username="' . $username . '"', '', 1);

        if (!$Target) {
            $msg = '<font color=red><b>such user cannot be found</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }

        $Tuid = $Target[0]['uid'];
        $result = dbInsert('gmembers', 'gid,uid,approved', '' . $groupId . ',' . $Tuid . ',1');
        if ($result) {
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
        } else {
            $msg = '<font color=red><b>operation failed, please contact the administrator</b></font>';
            $url = $_SERVER['HTTP_REFERER'];
            $style = 'alert_error';
            $toTime = 3000;
            include 'notice.php';
            exit;
        }
    }
}

//Dismiss or leave the group
if($admin) {
    if ($_POST['destroyubmitbtn']) {
        $result = dbDel('groups', 'gid='.$groupId . '');
        if ($result) {
            $msg = '<font color=red><b>operation succeeded</b></font>';
            //$url = $_SERVER['HTTP_REFERER'];
            header('location:group.php');
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
}
else{
    if ($_POST['leavesubmitbtn']) {
        $result = dbDel('gmembers', 'uid='.$_COOKIE['uid'].' and gid='.$groupId.'');
        if ($result) {
            $msg = '<font color=red><b>operation succeeded</b></font>';
            //$url = $_SERVER['HTTP_REFERER'];
            header('location:group.php?glist=1');
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
}

if(!empty( $_POST['applysubmitbtn'])){
    if (!empty($result)){
        $msg = '<font color=red><b>illegal operation</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

    $result=dbInsert('gmembers','gid,uid',''.$groupId.','.$_COOKIE['uid'].'');
    if($result){
        $msg = '<font color=green><b>apply is sent</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_right';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }else{
        $msg = '<font color=red><b>apply failed</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }
}

$title = 'Group Operation - '.WEB_NAME;
include template("group_info.html");

