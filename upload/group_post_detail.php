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

//读取帖子信息
$TiZi = dbSelect('gposts','*','pid='.$Id.' and isdel=0 and first=1','',1);
$authorid = $TiZi[0]['authorid'];		//作者ID
$Title = $TiZi[0]['title'];		//标题
$Content = $TiZi[0]['content'];		//内容
$Image = $TiZi[0]['image'];
$Video = $TiZi[0]['video'];
$Addtime = getFormatTime($TiZi[0]['addtime']);		//发布时间
$groupId = $TiZi[0]['gid'];		//版块ID
$Replycount = $TiZi[0]['replycount'];	//回复数量
$Hits = $TiZi[0]['hits'];//点击数量
if (!empty($TiZi[0]['voteoptions'])){
    $voteoptions = explode('+||+', $TiZi[0]['voteoptions']);
}else{
    $voteoptions = null;
}

$isadmin = isAdmin();
$result = dbSelect('gmembers','uid,approved,mute','uid='.$_COOKIE['uid'].' and gid='.$groupId.'','',1);
$approved = $result[0]['approved'];
$mute = $result[0]['mute'];
if(!$isadmin){
    if(!$result || $approved==0)
    {
        $msg = '<font color=red><b>You are not a member of the group<br>please apply for admission</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }
}

//vote result
if (!empty($voteoptions)) {
    $sql = 'select vote, count(vote) as votecount from '.DB_PREFIX.'voterecord where pid='.$Id.' group by vote';
    $voteresult = dbConn(trim($sql),true);
}




//$Elite = $TiZi[0]['elite'];		//精华
//$Rate = $TiZi[0]['rate'];			//所需积分数量

//读取上一条
$top = dbSelect('gposts','id','pid>'.$Id.' and isdel=0 and first=1 and gid ='.$groupId.'','id desc',1);
if($top)
{
    $topid=$top[0]['pid'];
}else{
    $topid=false;
}
//读取下一条
$down = dbSelect('gposts','id','pid<'.$Id.' and isdel=0 and first=1and gid ='.$groupId.'','id desc',1);
if($down){
    $downid = $down[0]['pid'];
}else{
    $downid = false;
}

$checkingroup = dbSelect('gmembers','uid','gid='.$groupId.' and approved=1');
if(!$checkingroup && !$isadmin){
    $msg = '<font color=red><b>you are not a member of the group</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}

$category = dbSelect('groups','gid,name,owner','gid='.$groupId.'','',1);
if($category)
{
    $smallName = $category[0]['name'];
    $smallId = $category[0]['gid'];
    $BanZhu = $category[0]['owner'];

}else{

    $msg = '<font color=red><b>illegal operation</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}

$isAuthor = $authorid==$_COOKIE['uid'];

if($isadmin||$_COOKIE['uid']==(int)$BanZhu){
    $GuanLi=1;
}else{
    $GuanLi=0;
}


$checkpermission = dbselect('gpostspermission','view,comment,addlink','pid='.$Id.' and uid='.$_COOKIE['uid'].'');
$viewPermit=$checkpermission[0]['view'];
$commentPermit=$checkpermission[0]['comment'];
$addlinkPermit=$checkpermission[0]['addlink'];

if($GuanLi){
    $viewPermit=1;
    $commentPermit=1;
    $addlinkPermit=1;
}

if(!$viewPermit){
    $msg = '<font color=red><b>you are not allowed to view the content</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
    exit;
}

//点击帖子时访问次数加1
$result = dbUpdate('gposts', 'hits=hits+1', 'pid='.$Id.' and isdel=0 and first=1');
if(!$result)
{
    $msg = '<font color=red><b>The post you are viewing does not exist or has been deleted</b></font>';
    $url = $_SERVER['HTTP_REFERER'];
    $style = 'alert_error';
    $toTime = 3000;
    include 'notice.php';
}

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
    //$A_utograph = $User[0]['autograph'];
    //$G_rade = $User[0]['grade'];
}

//该主题下的所有回复数量
$TZCount = dbFuncSelect('gposts','count(pid)','parentid='.$Id.' and isdel=0 and first=0');
$zCount = $TZCount['count(pid)'];
$linum = 10;
$Lpage = empty($_GET['page'])?1:$_GET['page'];
//循环帖子回复信息
$select = 't.pid as pid,t.isdisplay as isdisplay,t.authorid as authorid,t.content as content,t.addtime as addtime,t.isdel as isdel,u.username as username,u.email as email,u.udertype as udertype,u.regtime as regtime,u.lasttime as lasttime,u.picture as picture,t.image as image, t.video as video';
$HTiZi = dbDuoSelect('gposts as t','user as u',' on t.authorid=u.uid',null,null,$select,'t.parentid='.$Id.' and t.isdel=0 and t.first=0','t.pid asc', setLimit($linum));

$title = $Title.' - '.WEB_NAME;
$ggg = 'Concordia';

//保存帖子回复
if($_POST['replysubmit'])
{
    //判断用户是否登录
    if(!$_COOKIE['uid']){

        $notice='Sorry，you have not logged in';
        $url = 'index.php';
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

    if ($mute && !$isadmin){
        $msg = '<font color=red><b>you are not allowed to reply</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
    }

    if(!$commentPermit){

        $msg = '<font color=red><b>you are not allowed to comment on the content</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

    $parentid = $Id;					//跟帖时记录贴子ID
    $titleTmp = dbSelect('gposts','title','pid="'.$parentid.'"','id desc',1);
    $title = $titleTmp[0]['title'];
    $authorid = $_COOKIE['uid'];			//发布人ID
    $content = strMagic($_POST['message']);		//内容
    $picture = ($_FILES['pic']['error']>0)? null:upload('pic');
    $video = addslashes($_POST['video']);  //video
    $addtime = time();				//发表时间
    $groupId = $_POST['gid'];			//类别ID
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
    /*
    $regex = "((https?|ftp)\:\/\/)?"; // SCHEME
    $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
    $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
    $regex .= "(\:[0-9]{2,5})?"; // Port
    $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
    $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
    $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor

    if(preg_match("/^$regex$/", $contentcheck))
    {
        $msg = '<font color=red><b>you are not allowed to add link</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }
    */

    if (empty($content) && $picture==null){
        $msg = '<font color=red><b>please add content</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

    $n='first, parentid, authorid, title, content,image, video, addtime, gid';
    $v='0, '.$parentid.', '.$authorid.',"'.$title.'", "'.$content.'","'.$picture.'", "'.$video.'", '.$addtime.', '.$groupId.'';
    $result = dbInsert('gposts', $n, $v);

    $insert_id = dbSelect('gposts','pid','title="'.$title.'"','pid desc',1);
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
                $deleteresult = dbInsert('gpostdelete','pid,deletetime',''.$insertId.','.$deletetime.'');
                if($deleteresult){
                    $futuredelete=" will be deleted in ".$hourlater." hour ".$minutelater." minute later";
                }
            }
        }

        //更新帖子的回复数量[replycount]
        $result = dbUpdate('gposts', 'replycount=replycount+1', 'pid='.$parentid.'');

        //更新版块表的回复数量[replycount]
        $result = dbUpdate('groups', 'replycount=replycount+1', 'gid='.$groupId.'');
        //header('location:detail.php?id='.$Id);
        $msg = '<font color=red><b>Reply succeeded</b></font>'.$futuredelete;
        $url = 'group_post_detail.php?pid='.$Id;
        $style = 'alert_right';
        $toTime = 3000;
        include 'notice.php';
        exit;

    }

}

if ($_POST['newpostsubmitbtn']){
    if ($mute && !$isadmin){
        $msg = '<font color=red><b>you are not allowed to add new content</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
    }
    header('location:group_addc.php?gid='.$groupId.'&VNum='.$_POST['vote']);
}


$votecheck = dbSelect('voterecord','*','pid='.$Id.' and uid='.$_COOKIE['uid'].'');
if($_POST['votesubmit'])
{
    //判断用户是否登录
    if(!$_COOKIE['uid']){

        $notice='Sorry，you have not logged in';
        include 'close.php';
        exit;
    }

    if(!$commentPermit){

        $msg = '<font color=red><b>you are not allowed to vonte</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

    if($votecheck){

        $msg = '<font color=red><b>you have already voted</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

    $parentid = $Id;					//跟帖时记录贴子ID
    $groupId = $_POST['gid'];			//类别ID
    $vote=$_POST['voteselect'];

    $n='pid, uid, vote';
    $v=''.$Id.', '.$_COOKIE['uid'].', '.$vote.'';
    $result = dbInsert('voterecord', $n, $v);

    if(!$result)
    {
        $msg = '<font color=red><b>vote failed，please contact the administrator</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }else{
        $msg = '<font color=red><b>vote succeeded</b></font>';
        $url = 'group_post_detail.php?pid='.$Id;
        $style = 'alert_right';
        $toTime = 3000;
        include 'notice.php';
        exit;

    }

}

if($_POST['optionsubmit']) {
    //判断用户是否登录
    if (!$_COOKIE['uid']) {

        $notice = 'Sorry，you have not logged in';
        include 'close.php';
        exit;
    }

    if (!$commentPermit) {

        $msg = '<font color=red><b>you are not allowed to vonte</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

    if ($votecheck) {

        $msg = '<font color=red><b>you have already voted</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    }

    $parentid = $Id;                    //跟帖时记录贴子ID
    $groupId = $_POST['gid'];            //类别ID
    $option = $_POST['newoption'];

    $gpost = dbSelect('gposts','*','pid='.$Id.' and isdel=0 and first=1','',1);

    $newoptions = $gpost[0]['voteoptions'].'+||+'.$option;
    $result = dbUpdate('gposts', 'voteoptions="'.$newoptions.'"', 'pid='.$Id.'');

    if (!$result) {
        $msg = '<font color=red><b>add new option failed，please contact the administrator</b></font>';
        $url = $_SERVER['HTTP_REFERER'];
        $style = 'alert_error';
        $toTime = 3000;
        include 'notice.php';
        exit;
    } else {
        $msg = '<font color=red><b>add option succeeded</b></font>';
        $url = 'group_post_detail.php?pid=' . $Id;
        $style = 'alert_right';
        $toTime = 3000;
        include 'notice.php';
        exit;


    }
}



    //删除，放入回收站
    if(!empty($_GET['del'])&&($GuanLi||$isAuthor)){

        $result = dbUpdate('gposts', "isdel=1", 'pid='.$Id.'');
        $result = dbUpdate('gposts', "isdel=1", 'parentid = '.$Id.'');
        header('location:group_postlist.php?gid='.$groupId);

    }

    //删除回帖，放入回收站
    if(!empty($_GET['delht'])) {
        $replyauthor = dbSelect('gposts', 'authorid', 'pid=' . $_GET['hid'] . '');
        if ($GuanLi || $replyauthor[0]['authorid'] == $_COOKIE['uid']) {
            $result = dbUpdate('gposts', "isdel=1", 'pid=' . $_GET['hid'] . '');
            header('location:group_post_detail.php?pid=' . $Id);
        }
    }



include template("group_post_detail.html");
