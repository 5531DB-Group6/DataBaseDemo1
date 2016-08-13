<?php
/**
 * 注册
 */
	include './common/common.php';
	
	$key = trim($_GET['keywords']);
	if(!$key)
	{
		$msg = '<font color=red><b>no key word has been entered</b></font>';
		$url = $_SERVER['HTTP_REFERER'];
		$style = 'alert_error';
		$toTime = 3000;
		include 'notice.php';
		exit;
	}

	$arrKey = explode(' ',$key);

	$where = '';
	for($i=0; $i<count($arrKey); $i++)
	{
		if(empty($where))
		{
			$where .= "username like '%$arrKey[$i]%'";
		}else{
			$where .= " or username like '%$arrKey[$i]%'";
		}
	}

	$where = ' and (' . $where . ')';


	$uSearch = dbSelect('user', '*', 'status!=1' . $where);


	$where='';

	for($i=0; $i<count($arrKey); $i++)
	{
		if(empty($where))
		{
			$where .= "name like '%$arrKey[$i]%' or description like '%$arrKey[$i]%'";
		}else{
			$where .= " or name like '%$arrKey[$i]%' or description like '%$arrKey[$i]%'";
		}
	}

	//$where = ' and (' . $where . ')';

	$gSearch = dbSelect('groups', '*', $where);


	//$select = 't.id as id,t.authorid as authorid,t.content as content,t.addtime as addtime,t.title as title,t.classid as classid,t.replycount as replycount,t.hits as hits,u.username as username,c.classname as classname';
	//$search = dbDuoSelect('details as t','user as u','on t.authorid=u.uid','category as c','on c.cid=t.classid',$select,'first=1 and isdel=0 '.$where.'','id desc');

	$title = $key . 'search result - ' . WEB_NAME;

	include template("search.html");

?>
