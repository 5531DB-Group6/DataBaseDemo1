<?php
/**
 * 注册
 */
include './common/common.php';
include 'logincheck.php';
$title = 'Create Group - ' . WEB_NAME;


//验证是否为提交注册信息
if (!empty($_POST['regsubmit']))
{
    $gname = trim($_POST['groupname']);
    $description = trim($_POST['groupdescription']);

    //错误跳转页默认值
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;

    $alterNotice = false;	//提示页面标记位
    //验证用户名长度
    if(stringLen($gname,3,60))
    {
        $alterNotice = true;
        $msgArr[] = '<font color=red><b>Wrong length of username：consist of 3 to 12 characters</b></font>';
    }

    //判断数据库里是否存在这个用户名
    $exists = dbSelect('groups','gid', 'name="'.$gname.'"','uid desc',1);
    if($exists)
    {
        $alterNotice = true;
        $msgArr[] = '<font color=red><b>group name has been used</b></font>';
    }

    //验证是否需要显示提示信息
    if($alterNotice)
    {
        $msg = join('<br />', $msgArr);
        include 'notice.php';
        exit;
    }

    //创建用户
    $uid = $_COOKIE['uid'];
    $n = 'name, owner,description';
    $v = '"'.$gname.'", "'.$uid.'","'.$description.'"';
    $result = dbInsert('groups', $n, $v);

    if(!$result)
    {
        $msg = '<font color=red><b>Failed to create the group, pleas contact the administrator</b></font>';
        include 'notice.php';
    }else{
        //注册成功后自动登录
        $result = dbSelect('groups','gid', 'name="'.$gname.'"', 'gid desc', 1);
        $gid = $result[0]['gid'];
        echo $gid;
        $n = 'gid, uid,approved,admin';
        $v = ''.$gid.','.$uid.',1,1';
        $insertGM = dbInsert('gmembers', $n, $v);

        $msg = '<font color=green><b>Thanks for your registration, now you will login as a member</b></font>';
        $url = 'group_postlist.php?gid='.$gid.'';
        $style = 'alert_right';
        include 'notice.php';

    }

}else{
    include template("group_reg.html");
}

?>
