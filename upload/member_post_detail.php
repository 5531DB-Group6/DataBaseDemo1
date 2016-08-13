<?php
/**
 * Poat detail
 */

include './common/common.php';
include 'logincheck.php';

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

//点击帖子时访问次数加1
$result = dbUpdate('uposts', 'hits=hits+1', 'pid='.$Id.' and isdel=0 and first=1 and isdisplay=1');
if(!$result)
{
    $msg = '<font color=red><b>The post you are viewing does not exist or has been deleted/suspended</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
}

//读取帖子信息
$TiZi = dbSelect('uposts','*','pid='.$Id.' and isdel=0 and first=1 and isdisplay=1','',1);
$authorid = $TiZi[0]['authorid'];		//作者ID
$Title = $TiZi[0]['title'];		//标题
$Content = $TiZi[0]['content'];		//内容
$Image = $TiZi[0]['image'];
$Video = $TiZi[0]['video'];
$Addtime = getFormatTime($TiZi[0]['addtime']);		//发布时间
$Replycount = $TiZi[0]['replycount'];	//回复数量
$Hits = $TiZi[0]['hits'];//点击数量


//读取上一条
$top = dbSelect('gposts','id','pid>'.$Id.' and isdel=0 and first=1 and authorid ='.$authorid.'','id desc',1);
if($top)
{
    $topid=$top[0]['pid'];
}else{
    $topid=false;
}
//读取下一条
$down = dbSelect('gposts','id','pid<'.$Id.' and isdel=0 and first=1and authorid ='.$authorid.'','id desc',1);
if($down){
    $downid = $down[0]['pid'];
}else{
    $downid = false;
}

$uid=$_COOKIE['uid'];
$admin = dbSelect('user','udertype','uid='.$_COOKIE['uid'].'');
$isadmin = $admin[0]['udertype'];
$isOwner = ($authorid==$_COOKIE['uid']) || $admin[0]['udertype'];



//读取会员信息
$User = dbSelect('user','username,email,udertype,regtime,lasttime,picture','uid='.$authorid.'','',1);
if($User)
{
    $U_sername = $User[0]['username'];
    $E_mail = $User[0]['email'];
    $U_dertype = $User[0]['udertype'];
    $R_egtime = formatTime($User[0]['regtime'],false);
    $L_asttime = formatTime($User[0]['lasttime'],false);
    $P_icture = $User[0]['picture'];
}


$friendcheck = dbSelect('friend','*','uid='.$authorid.' and fid='.$_COOKIE['uid'].' and approved=1');
$groupmatecheck = dbDuoSelect('gmembers as g1','gmembers as g2','on g1.gid=g2.gid',null,null,'g2.uid as uid, g2.gid as gid','g1.uid='.$authorid.' and g2.uid='.$_COOKIE['uid'].'');
if(!empty($friendcheck)||!empty($groupmatecheck)){
    $checkpermission = dbselect('upostspermission','view,comment,addlink','pid='.$Id.' and uid='.$_COOKIE['uid'].'');
    if (!empty($checkpermission)){
        $viewPermit=$checkpermission[0]['view'];
        $commentPermit=$checkpermission[0]['comment'];
        $addlinkPermit=$checkpermission[0]['addpermit'];
    }else{
        $viewPermit=1;
        $commentPermit=1;
        $addlinkPermit=1;
    }
}else{
    $checkpermission = dbSelect('upostspermissionpublic','view,comment,addlink','pid='.$Id.'');
    if (!empty($checkpermission)){
        $viewPermit=$checkpermission[0]['view'];
        $commentPermit=$checkpermission[0]['comment'];
        $addlinkPermit=$checkpermission[0]['addlink'];
    }else{
        $viewPermit=1;
        $commentPermit=1;
        $addlinkPermit=1;
    }

}
if($isadmin){
    $viewPermit=1;
    $commentPermit=1;
    $addlinkPermit=1;
}

if(!$viewPermit){
    $msg = '<font color=red><b>Sorry, you are not allowed to read this content</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

//该主题下的所有回复数量
$TZCount = dbFuncSelect('uposts','count(pid)','parentid='.$Id.' and isdel=0 and first=0 and isdiplay=1');
$zCount = $TZCount['count(pid)'];
$linum = 10;
$Lpage = empty($_GET['page'])?1:$_GET['page'];
//循环帖子回复信息
$select = 't.pid as pid,t.isdisplay as isdisplay,t.authorid as authorid,t.content as content,t.addtime as addtime,t.isdel as isdel,u.username as username,u.email as email,u.udertype as udertype,u.regtime as regtime,u.lasttime as lasttime,u.picture as picture,t.image as image, t.video as video';
$HTiZi = dbDuoSelect('uposts as t','user as u',' on t.authorid=u.uid',null,null,$select,'t.parentid='.$Id.' and t.isdel=0 and t.first=0','t.pid asc');

//保存帖子回复
if($_POST['replysubmit'])
{
    //判断用户是否登录
    if(!$_COOKIE['uid']){

        $notice='Sorry，you have not logged in';
        include 'close.php';
        exit;
    }

    if(!$commentPermit){
        $msg = '<font color=red><b>Sorry, you are not allowed to comment on this content</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

    $parentid = $Id;					//跟帖时记录贴子ID
    $titleTmp = dbSelect('uposts','title','pid="'.$parentid.'"','id desc',1);
    $title = $titleTmp[0]['title'];
    $ReplyAuthorid = $_COOKIE['uid'];			//发布人ID
    $content = strMagic($_POST['message']);		//内容
    $video = strMagic($_POST['video']);  //video
    $picture = ($_FILES['pic']['error']>0)? null:upload('pic');
    $addtime = time();				//发表时间
    $futuredelete = "";

    $contentcheck = (string)$content;
    $bHasLink = strpos($contentcheck, 'http') !== false || strpos($contentcheck, 'www.') !== false;

    if($bHasLink && !$addlinkPermit){
        $msg = '<font color=red><b>you are not allowed to add link</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

    if (empty($content) && $picture==null){
        $msg = '<font color=red><b>please add content</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

    $n='first, parentid, authorid, title, content,image, video, addtime';
    $v='0, '.$parentid.', '.$ReplyAuthorid.',"'.$title.'", "'.$content.'","'.$picture.'", "'.$video.'", '.$addtime.'';
    $result = dbInsert('uposts', $n, $v);

    $insert_id = dbSelect('uposts','pid','title="'.$title.'"','pid desc',1);
    $insertId = $insert_id[0]['pid'];

    if(!$result)
    {
        $msg = '<font color=red><b>Reply failed，please contact the administrator</b></font>';
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

        //更新帖子的回复数量[replycount]
        $result = dbUpdate('uposts', 'replycount=replycount+1', 'pid='.$parentid.'');

        //更新版块表的回复数量[replycount]
        $msg = '<font color=red><b>Reply succeeded</b></font>'.$futuredelete;
        $url = 'member_post_detail.php?pid='.$Id.'';
        $style = 'alert_right';
        $toTime = 3000;
        include 'notice.php';
        /*
        $msg = '回帖赠送';
        include 'layer.php';
        */
        exit;

    }

}


$title = $Title.' - '.WEB_NAME;
$ggg = 'COMP 5531';


//查找版主或管理员
/*
$NBanZhu = explode(',',$BanZhu);
if(in_array($_COOKIE['uid'], $NBanZhu))
{
    $GuanLi=true;
}else{
    if($_COOKIE['udertype'])
    {
        $GuanLi=true;
    }
}
*/

    //删除，放入回收站
    if(!empty($_GET['del'])&&$isOwner){

        $result = dbUpdate('uposts', "isdel=1", 'pid='.$Id.'');
        $result = dbUpdate('uposts', "isdel=1", 'parentid = '.$Id.'');
        header('location:member_postlist.php?uid='.$authorid.'');

    }
    //删除回帖，放入回收站
    if(!empty($_GET['delht']) ){
        $replyauthor = dbSelect('uposts','authorid','pid='.$_GET['hid'].'');
        if ($isadmin || $replyauthor[0]['authorid']==$_COOKIE['uid']) {
            $result = dbUpdate('uposts', "isdel=1", 'pid=' . $_GET['hid'] . '');
            header('location:member_post_detail.php?pid=' . $Id);
        }

    }


include template("member_post_detail.html");
