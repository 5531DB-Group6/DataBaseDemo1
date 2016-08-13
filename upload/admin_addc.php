<?php
/**
 * Add posts
 */

include './common/common.php';
include 'logincheck.php';


$isadmin = isAdmin();
if (!$isadmin){
    $msg = '<font color=red><b>You are not an administrator</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}


//发布帖子
if($_POST['topicsubmit'])
{
    $authorid = $_COOKIE['uid'];		//发布人ID
    $title = strMagic($_POST['subject']);		//标题
    $content = strMagic($_POST['content']);		//内容
    $addtime = time();			//发表时间
    $groupId = $_POST['gid'];		//类别ID
    $picture = ($_FILES['pic']['error']>0)? null:upload('pic');

    if(empty($title)) {
        $msg = '<font color=red><b>please add a title</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

    $n = 'authorid, title, content,image, addtime';
    $v = ''.$authorid.', "'.$title.'", "'.$content.'", "'.$picture.'" ,'.$addtime.'';
    $result = dbInsert('pposts', $n, $v);

    $insert_id = dbSelect('pposts','pid','title="'.$title.'"','pid desc',1);
    $insertId = $insert_id[0]['pid'];
    if(!$result){

        $msg = '<font color=red><b>Posting failed, please contact the administrator</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;

    }else{
        $allusers = dbSelect('user','uid, username',1);
        foreach ($allusers as $user){
            $senderid = $authorid;
            $receiverid = $user['uid'];
            $emailtitle = "new post from public information: ".$title;
            $emailtitlecontent = $content;
            $sendtime = time();

            $n = 'senderid, receiverid, title, content, sendtime';
            $v = ''.$senderid.', '.$receiverid.', "'.$emailtitle.'", "'.$emailtitlecontent.'", '.$sendtime.'';
            $result = dbInsert('mails', $n, $v);
        }

        $msg = '<font color=red><b>Posting succeeded</b></font>';
        $url = 'admin_post_detail.php?pid='.$insertId;
        $style = 'alert_right';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }
}


$title = 'Add Posts'.$OnCname.' - '.WEB_NAME;
$menu = WEB_NAME;
include template("admin_addc.html");

