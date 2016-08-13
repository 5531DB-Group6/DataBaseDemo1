<?php
/**
 * 个人资料
 */

include './common/common.php';
include 'logincheck.php';
//判断用户是否登录
if(empty($_COOKIE['uid']))
{
    $msg = '<font color=red><b>You have not logged in</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}

//读取个人资料
$result = dbSelect('user','*', 'uid='.$_COOKIE['uid'].' and status!=2','',1);
$visible = dbSelect('profilevisible','*', 'uid='.$_COOKIE['uid'].' ','',1);
if(!$result)
{
    $msg = '<font color=red><b>The user does not exist or is banded by the administrator</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}

$select='u.uid as uid, u.username as username, u.picture as picture';
$FriendList = DBduoSelect('user as u','friend as f','on u.uid = f.fid and f.approved=1',null,null,$select,'f.uid ='.$_COOKIE['uid'].'','u.username asc');

$sql = 'select * from '.DB_PREFIX.'user where uid in (select distinct g2.uid from  '.DB_PREFIX.'gmembers g1, '.DB_PREFIX.'gmembers g2 where g1.uid='.$_COOKIE['uid'].' and g1.gid=g2.gid and g2.uid!='.$_COOKIE['uid'].' and g1.approved=1 and g2.approved=1 and g2.uid not in (select fid from '.DB_PREFIX.'friend where uid='.$_COOKIE['uid'].'))';
$GroupMatesList = dbConn(trim($sql), true);


if($_POST['visibilitysubmitbtn'])
{
    if (is_array($FriendList)) {
        foreach ($FriendList as $key => $val) {
            $vpermissionempty = $_POST['vpermission' . $val['uid']];
            $firstname = $_POST['firstnamevisible' . $val['uid']];
            $lastname = $_POST['lastnamevisible' . $val['uid']];
            $sex = $_POST['gendervisible' . $val['uid']];
            $birth = $_POST['birthdayvisible' . $val['uid']];
            $address = $_POST['addressvisible' . $val['uid']];
            $place = $_POST['regionvisible' . $val['uid']];
            $profession = $_POST['professionvisible' . $val['uid']];
            if ($vpermissionempty) {
                $fresult = dbInsert('profilevisiblemember', 'uid, tid, firstname_visible, lastname_visible, sex_visible, bday_visible, address_visible, place_visible, profession_visible', '' . $_COOKIE['uid'] . ',' . $val['uid'] . ', ' . $firstname . ',' . $lastname . ', ' . $sex . ', ' . $birth . ',' . $address . ',' . $place . ',' . $profession . '');
            } else {
                $fresult = dbupdate('profilevisiblemember', 'firstname_visible=' . $firstname . ',lastname_visible=' . $lastname . ', sex_visible=' . $sex . ', bday_visible=' . $birth . ', address_visible=' . $address . ', place_visible=' . $place . ', profession_visible=' . $profession . '', 'uid=' . $_COOKIE['uid'] . ' and tid=' . $val['uid'] . '');
            }
        }
    }

    if (is_array($GroupMatesList)) {
        foreach ($GroupMatesList as $key => $val) {
            $vpermissionempty = $_POST['vpermission' . $val['uid']];
            $firstname = $_POST['firstnamevisible' . $val['uid']];
            $lastname = $_POST['lastnamevisible' . $val['uid']];
            $sex = $_POST['gendervisible' . $val['uid']];
            $birth = $_POST['birthdayvisible' . $val['uid']];
            $address = $_POST['addressvisible' . $val['uid']];
            $place = $_POST['regionvisible' . $val['uid']];
            $profession = $_POST['professionvisible' . $val['uid']];
            if ($vpermissionempty) {
                $gresult = dbInsert('profilevisiblemember', 'uid, tid, firstname_visible, lastname_visible, sex_visible, bday_visible, address_visible, place_visible, profession_visible', '' . $_COOKIE['uid'] . ',' . $val['uid'] . ', ' . $firstname . ',' . $lastname . ', ' . $sex . ', ' . $birth . ',' . $address . ',' . $place . ',' . $profession . '');
            } else {
                $gresult = dbupdate('profilevisiblemember', 'firstname_visible=' . $firstname . ',lastname_visible=' . $lastname . ', sex_visible=' . $sex . ', bday_visible=' . $birth . ', address_visible=' . $address . ', place_visible=' . $place . ', profession_visible=' . $profession . '', 'uid=' . $_COOKIE['uid'] . ' and tid=' . $val['uid'] . '');
            }
        }
    }

    //header('location:home.php');

}


$title = 'Personal info - '.WEB_NAME;
include template("home_infopermission.html");

