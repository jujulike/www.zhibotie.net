<?php

/**
 * 通用app操作
 */


if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$option = strtolower($_GET['option']);

switch ($option){
	case "login":
				
		break;
	case "atuser":
		if($_SGLOBAL['supe_uid']){
			//获取好友
			$query = $_SGLOBAL['db']->query("SELECT friend FROM ".tname("spacefield")." WHERE uid='{$_SGLOBAL['supe_uid']}'");
			$user = $_SGLOBAL['db']->fetch_array($query);
			if($user && $user['friend']){
				$query = $_SGLOBAL['db']->query("SELECT s.uid,s.username as name,f.spacenote FROM ".tname("space")." s LEFT JOIN ".tname("spacefield")." f ON s.uid=f.uid WHERE s.uid in ({$user['friend']}) ORDER BY s.lastlogin DESC");
				while($value = $_SGLOBAL['db']->fetch_array($query)){
					$value['head'] = avatar($value['uid'],"small",true);
					$list[] = $value;
				}
				
				echo ajax_return(1,"success",$list);
			}else{
				echo ajax_return(0,"你没有好友，去找找朋友吧：）");
			}
			//echo ajax_return(1,"success",$_SGLOBAL);
			
		}else{
			echo ajax_return(0,"未登录");
		}
		exit();
		break;
}

include template("apps/{$m}/{$apps_config[tpl]}{$option}");