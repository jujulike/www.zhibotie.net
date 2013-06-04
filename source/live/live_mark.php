<?php
/**
 * @param 内裤叔叔 分离 live.php 文件
 * @param mark帖子
 */


if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}



$num = $_GET['num'];
$pid = $_GET['pid'];
if($num && $tid){
	$uid = (empty($uid))?0:$uid;
	$myip = getonlineip(1);
	$join = ($uid == 0)?"ip='{$myip}' AND uid='{$uid}'":"uid='{$uid}'";
	
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("mark"). " WHERE {$join} AND tid='{$tid}' AND type='www' ORDER BY dateline DESC");
	$rest = $_SGLOBAL['db']->fetch_array($query);
	if($rest){
		updatetable("mark",array("num"=>$num,"dateline"=>time(),"type"=>"www","pid"=>$pid),array("id"=>$rest['id']));
		echo ajax_return(1,"update");
	}else{
		inserttable("mark",array("uid"=>$uid,"ip"=>$myip,"type"=>"www","pid"=>$pid,"tid"=>$tid,"num"=>$num,"dateline"=>time()));
		echo ajax_return(1,"mark");
	}
}
exit();