<?php

/**
 * live.php ajax 操作
 */


if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$option = $function->get_gp("option","G");

@include_once(S_ROOT."./source/function_date.php");
$date = new dateService();
switch ($option){
	case "delete_posts":
		$get = $function->get_gp(array("id","pid"),"G");
		$tid = $get['id'];
		$pid = $get['pid'];
		if(!$tid || !$pid){
			exit(ajax_return(0,"非法操作，请重新尝试"));
		}else{
			//开始查询有多少次
			$query = $_SGLOBAL['db']->query("SELECT `delete` FROM ".tname("posts")." WHERE pid='{$pid}' ");
            $result = $_SGLOBAL['db']->fetch_array($query);
            if($result && $result['delete'] < 10){
                updatetable("posts",array("delete"=>$result['delete'] + 1),array("pid"=>$pid));
                exit(ajax_return(1));       
            }elseif($result['delete'] >= 10){
                exit(ajax_return(2));
            }
		}
		break;
	case "get_comment_list":
		$pid = $function->get_gp("pid","G");
		if($pid){
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("postscomment"). " WHERE pid='{$pid}' LIMIT 0,50");
			while($value = $_SGLOBAL['db']->fetch_array($query)){
				$value['dateline'] = $date->diff($value['dateline']?$value['dateline']:time());
				$data[] = $value;
			}
			$data = is_array($data) && $data ? $data : array();
			exit(ajax_return(1,"succeed",$data));
		}else{
			exit(ajax_return(0));
		}
		break;
	case "post_comment_list":
			$get = $function->get_gp(array("pid","text","username","reply_id","isreply"),"P");
			$pid = $get['pid'];
			$content = js_unescape($get['text']);
			$username = js_unescape($get['username']);
			$reply_id = $get['reply_id'];
			$isreply = $get['isreply'];
			$reply_name = js_unescape($_POST['reply_name']);
			$t = time();
			if($pid && $content){
				$uid = $_SGLOBAL['supe_uid']?$_SGLOBAL['supe_uid']:0;
				$username = $username?$username:"匿名游客";
				$data['pid'] = $pid;
				$data['content'] = $content;
				$data['uid'] = $uid;
				$data['username'] = $username;
				$data['recontent'] = $isreply?"{$pid}*$*$*{$reply_id}*$*$*{$reply_name}":"";
				$data['isreply'] = $isreply?$isreply:0;
				$data['dateline'] = $t;
				$data['ip'] = getonlineip(1);
				$data['id'] = inserttable("postscomment", $data ,1);
				$sql = "SELECT * FROM ".tname("postsfield")." WHERE pid='{$pid}' limit 1";
				$return = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query($sql));
				if($return){
					$_SGLOBAL['db']->query("UPDATE ".tname("postsfield")." SET replies=replies+1 WHERE pid='{$pid}'");
				}else{
					inserttable("postsfield", array("pid"=>$pid,"replies"=>1));
				}
				$data['dateline'] = $date->diff($t);
				exit(ajax_return(1,"succeed",array(0=>$data)));
			}else{
				exit(ajax_return(0,"非法操作"));
			}
			break;
	case "clearCookies":
		ssetcookie("tid_history","",259200);
		ssetcookie("date_history","",259200);
		exit(ajax_return(1,"succeed"));
		break;
}

