<?php
/**
 * @param 内裤叔叔 分离 live.php 文件
 * @param 热门帖子
 */


if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}


include_once(S_ROOT.'./source/functionInit.php');
$function = new functionInit();
$pagesize = 20;
if($page > 1){
	$offer = ($page - 1 ) * $pagesize;
}else{
	$offer = 0;
}

$orderby = !empty($_GET['orderby'])?$_GET['orderby']:"getdate";
$action = array();
$action['replies'] = ($orderby == "replies")?"class=\"active\"":"";
$action['views'] = ($orderby == "views")?"class=\"active\"":"";
$action['getdate'] = ($orderby == "getdate")?"class=\"active\"":"";
$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT count(*) FROM ".tname("threads")." WHERE (replies / views ) * 100 >= 50"));
//echo "<!--SELECT * FROM ".tname("threads")." WHERE (replies / views ) * 100 >= 50 ORDER BY {$orderby} DESC LIMIT $offer,$pagesize-->";
$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("threads")." WHERE (replies / views ) * 100 >= 50 ORDER BY {$orderby} DESC LIMIT $offer,$pagesize");
while($value = $_SGLOBAL['db']->fetch_array($query)){
	//$value['tid'] = $cry->php_encrypt($value['tid']);
	$value['subject'] = cubstr_same($value['subject']);
	$value['date']  = $date->diff(empty($value['getdate'])?time():$value['getdate']);
	$value['message'] = replace_ubb_html(trim($value['message']));
	$value['message'] = ($value['fid'] == 1)?preg_replace("/\&lt\;(.*?)\&gt\;/", "",$value['message']):$value['message'];
	$value['message'] = $function->cutstr($value["message"], 200, ".....");
	$hotlist[] = $value;
}
$multi = multi($count,$pagesize, $page, "live.php?do=hot&orderby={$orderby}");