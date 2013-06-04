<?php
/**
 * @param 内裤叔叔 分离 live.php 文件
 * @param 我的mark帖子
 */


if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

include_once(S_ROOT.'./source/functionInit.php');
$function = new functionInit();
$pagesize = 100;
if($page > 1){
	$offer = ($page - 1 ) * $pagesize;
}else{
	$offer = 0;
}

$uid = (empty($uid))?0:$uid;
$myip = getonlineip(1);
$join = ($uid == 0)?"m.ip='{$myip}' AND m.uid='{$uid}'":"m.uid='{$uid}'";
$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname("mark"). " m  WHERE {$join} AND type='www'"));
$query = $_SGLOBAL['db']->query("SELECT m.*,m.dateline as marktime,t.* FROM ".tname("mark"). " m,".tname("threads")." t WHERE {$join} AND m.tid=t.tid AND type='www' ORDER BY m.dateline DESC LIMIT $offer,$pagesize");
while($value = $_SGLOBAL['db']->fetch_array($query)){
	if($value['num'] <= $pagesize){
		$value['mypage'] = 1;
	}else{
		$value['mypage'] = ceil($value['num'] / $pagesize);
	}
	$value['message'] = replace_ubb_html(trim($value['message']));
	$value['message'] = ($value['fid'] == 1)?preg_replace("/\&lt\;(.*?)\&gt\;/", "",$value['message']):$value['message'];
	$value['message'] = $function->cutstr($value['message'], 200, ".....");
	$value['subject'] = cubstr_same($value['subject']);
	$value['date']  = $date->diff(empty($value['marktime'])?time():$value['marktime']);
	$mymark[] = $value;
}

$multi = multi($count,$pagesize, $page, "live.php?do=mymark");