<?php
/**
 * @param 内裤叔叔 分离 live.php 文件
 * @param 评论
 */


if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}



if(submitcheck("postsubmit")){
	$message = getstr($_POST['message'], 0, 1, 1, 1, 2);
	if(strlen($message) < 2) {
		showmessage('content_is_too_short');
	}
	$threads['tid'] = mysql_real_escape_string($_POST['tid']);
	$threads['message'] = $message;
	$threads['hash'] = md5($_SGLOBAL['supe_uid']);
	$threads['author'] = mysql_real_escape_string($_SGLOBAL['supe_username']);
	$threads['status'] = $theards['subject'] = $theards['head'] = '';
	$threads['lastpost'] = time();
	$threads['frist'] = 0;
	$threads['head'] = $_SGLOBAL['supe_uid'];
	$query = inserttable('posts', $threads,1);
	$_SGLOBAL['db']->query("UPDATE ".tname("threads"). " SET replies=replies+1 WHERE tid='$threads[tid]'");
	if($query){
		$my = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query("SELECT * FROM ".tname("ireply")." WHERE uid='$_SGLOBAL[supe_uid]' AND tid='$threads[tid]' LIMIT 1"));
		if(!$my){
			inserttable("ireply",array("uid"=>$_SGLOBAL["supe_uid"],"tid"=>$threads['tid'],"message"=>$message,"lastpost"=>time()));
		}else{
			updatetable("ireply",array("lastpost"=>time(),"message"=>$message),array("uid"=>$_SGLOBAL["supe_uid"],"tid"=>$threads["tid"]));
		}
		showmessage('do_success',"live.php?do=view&tid=$threads[tid]", 1);
	}
}else{
	showmessage('non_normal_operation',"live.php?do=view&tid=$threads[tid]",1);
	 
}
exit();