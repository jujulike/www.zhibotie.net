<?php
/**
 * @param 内裤叔叔 分离 live.php 文件
 * @param 删除我的mark帖子
 */


if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$id = mysql_real_escape_string($_GET['id']);
$uid = (empty($uid))?0:$uid;
if($id){
	$_SGLOBAL['db']->query("DELETE FROM ".tname("mark"). " WHERE id ='$id' and uid='{$uid}'");
	echo ajax_return(1);
	exit();
}else{
	echo ajax_return(0);
	exit();
}