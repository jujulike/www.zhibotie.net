<?php
/**
 * @param 内裤叔叔 2012.09.13 加入此项用于 历史记录的浏览
 */

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

include_once(S_ROOT.'./source/functionInit.php');
$function = new functionInit();
$page = isset($_GET['page'])?$_GET['page']:1;
$pagesize = 20;

//获取COOKES
$a_tid = $a_date = array();
$c_tid = $_SCOOKIE['tid_history'];
$c_date = $_SCOOKIE['date_history'];
$a_tid = explode("|",$c_tid);
$a_date = explode("|",$c_date);
//循环去重，重组数组
foreach ($a_tid as $key => $value){
	if($value){
		$cookies[$value]['tid'] = $value;
		$cookies[$value]['dateline'] = $a_date[$key];
	}
}

//数组分页取值
$cookies_page = array_slice($cookies, ($page-1) * $pagesize , $pagesize ,TRUE);

$count = count($cookies);
//排序

$cookies_tid = multi_array_sort($cookies_page,"dateline",SORT_DESC);

foreach($cookies_tid as $key => $v){
	if($v['tid']){
		$tidlist[] = $v['tid'];
	}
}

$tid = implode(",",$tidlist);

//统计
if($tid){
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("threads")." WHERE tid IN({$tid}) ORDER BY field(tid,{$tid})");
	while($value = $_SGLOBAL['db']->fetch_array($query)){
		//$value['tid'] = $cry->php_encrypt($value['tid']);
		$value['subject'] = cubstr_same($value['subject']);
		$value['date']  = $date->diff(empty($value['getdate'])?time():$value['getdate']);
		$value['message'] = replace_ubb_html(trim($value['message']));
		$value['message'] = ($value['fid'] == 1)?preg_replace("/\&lt\;(.*?)\&gt\;/", "",$value['message']):$value['message'];
		$value['message'] = $function->cutstr($value['message'], 200, ".....");
		$history[] = $value;
	}
	$multi = multi($count,$pagesize, $page, "live.php?do=history");
}else{
	$history = array();
}


function multi_array_sort($multi_array,$sort_key,$sort=SORT_ASC){
	if(is_array($multi_array)){
		foreach ($multi_array as $row_array){
			if(is_array($row_array)){
				$key_array[] = $row_array[$sort_key];
			}else{
				return -1;
			}
		}
	}else{
		return -1;
	}
	array_multisort($key_array,$sort,$multi_array);
	return $multi_array;
}