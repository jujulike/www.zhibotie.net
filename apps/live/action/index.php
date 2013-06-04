<?php

/*
 * 用于直播贴其他操作；
 */

$option = $_GET['option'];
$option = empty($option)?"index":$option;

switch ($option){
	case "downtxt":
		$tid = mysql_real_escape_string($_GET['tid']);
		$hash = $_GET['hash'];
		if($tid && is_numeric($tid) && $hash == formhash()){
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("threads")." where tid='{$tid}'");
			$rest = $_SGLOBAL['db']->fetch_array($query);
			if(!$rest){
				echo "Sorry, 404!";
				exit();
			}
			$echoms = "";
			$echoms .= $rest['subject']."\r\n"; 
			$echoms .= $rest['message']."\r\n";
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("posts")." where tid='{$tid}' AND `hash`='{$rest['hash']}'");
			while($value =  $_SGLOBAL['db']->fetch_array($query)){
				$echoms .=  $value['message']."\r\n";
			}
			Header( "Content-type:application/octet-stream "); 
			Header( "Accept-Ranges:bytes"); 
			header( "Content-Disposition:attachment;filename={$rest['subject']}. —— www.zhibotie.net.txt"); 
			header( "Expires:   0 "); 
			header( "Cache-Control:   must-revalidate,   post-check=0,   pre-check=0 "); 
			header( "Pragma:   public "); 
			echo $echoms;
			exit();
		}else{
			echo "非法操作";
			exit();
		}
		break;
}