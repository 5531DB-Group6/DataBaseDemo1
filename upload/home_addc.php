<?php
/**
 * Add posts
 */

include './common/common.php';
include 'logincheck.php';

$uid = $_COOKIE['uid'];

$select='u.uid as uid, u.username as username, u.picture as picture';
$FriendList = DBduoSelect('user as u','friend as f','on u.uid = f.fid and f.approved=1',null,null,$select,'f.uid ='.$_COOKIE['uid'].'','u.username asc');

$sql = 'select * from '.DB_PREFIX.'user where uid in (select distinct g2.uid from  '.DB_PREFIX.'gmembers g1, '.DB_PREFIX.'gmembers g2 where g1.uid='.$_COOKIE['uid'].' and g1.gid=g2.gid and g2.uid!='.$_COOKIE['uid'].' and g2.uid not in (select fid from '.DB_PREFIX.'friend where uid='.$_COOKIE['uid'].'))';
$GroupMatesList = dbConn(trim($sql), true);

//发布帖子
if($_POST['topicsubmit'])
{
    $authorid = $_COOKIE['uid'];		//发布人ID
    $title = strMagic($_POST['subject']);		//标题
    $content = strMagic($_POST['content']);		//内容
    $video = strMagic($_POST['video']);  //video
    $addtime = time();			//发表时间
    $picture = ($_FILES['pic']['error']>0)? null:upload('pic');
    $futuredelete = "";

    if(empty($title)) {
        $msg = '<font color=red><b>please add a title</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        //$toTime = 3000;
        include 'notice.php';
        exit;
    }

    $n = 'first, authorid, title, content,image,video, addtime';
    $v = '1, '.$authorid.', "'.$title.'", "'.$content.'", "'.$picture.'" ,"'.$video.'",'.$addtime.'';
    $result = dbInsert('uposts', $n, $v);

    $insert_id = dbSelect('uposts','pid','title="'.$title.'"','pid desc',1);
    $insertId = $insert_id[0]['pid'];
    if(!$result){

        $msg = '<font color=red><b>Post  failed, please contact the administrator</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;

    }else{

        if(isset($_POST['deletelater'])) {
            $hourlater = intval($_POST['hourlater']) ;
            $minutelater =intval($_POST['minutelater']) ;
            if(is_int($hourlater) && is_int($minutelater)){
                $deletetime = time()+$hourlater*60*60+$minutelater*60;
                $deleteresult = dbInsert('upostdelete','pid,deletetime',''.$insertId.','.$deletetime.'');
                if($deleteresult){
                    $futuredelete=" will be deleted in ".$hourlater." hour ".$minutelater." minute later";
                }
            }
        }

        $permission = $_POST['permissionpublic'];
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
        $presult = dbInsert('upostspermissionpublic', 'pid,view,comment,addlink', '' . $insertId . ',' . $view . ',' . $comment . ',' . $addlink . '');

        if(is_array($FriendList)) {
            foreach ($FriendList AS $key => $val) {
                $uid = $val['uid'];
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
                $presult = dbInsert('upostspermission', 'uid,pid,view,comment,addlink', '' . $uid . ',' . $insertId . ',' . $view . ',' . $comment . ',' . $addlink . '');
            }
        }

        if(is_array($GroupMatesList)){
            foreach($GroupMatesList AS $key=>$val){
                $uid = $val['uid'];
                $permission = $_POST['permission'.$uid.''];
                switch ($permission){
                    case 0:
                        $view =0;
                        $comment=0;
                        $addlink=0;
                        break;
                    case 1:
                        $view =1;
                        $comment=0;
                        $addlink=0;
                        break;
                    case 2:
                        $view =1;
                        $comment=1;
                        $addlink=0;
                        break;
                    case 3:
                        $view =1;
                        $comment=1;
                        $addlink=1;
                        break;
                }
                $presult = dbInsert('upostspermission','uid,pid,view,comment,addlink',''.$uid.','.$insertId.','.$view.','.$comment.','.$addlink.'');
            }
        }



        $msg = '<font color=red><b>Posting succeeded</b></font>'.$futuredelete;
        $url = 'home_postlist.php';
        $style = 'alert_right';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

}


$title = 'Add Posts'.$uid.' - '.WEB_NAME;
$menu = WEB_NAME;
include template("home_addc.html");

