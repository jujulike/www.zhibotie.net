<?php
/**
 * @param 内裤叔叔 分离 live.php 文件
 * @param 帖子列表
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

$list = array();
$wheresql = "where ";


$key = isset($_GET['key'])?$_GET['key']:"";

if($do == "all"){
	$wheresql .= "1";
	$key ="";
	$title_key = ($key)?$key."_":"";
}elseif($do == "douban"){
	$wheresql .= " fid ='0' ";
	//$key = $douban_array[$key];
	$title_key = ($key)?$douban_array[$key]."_":"";
}elseif($do == "tianya"){
	$wheresql .= " fid ='2' ";
	$key = $tianya_array[$key];
	$title_key = ($key)?$key."_":"";
}elseif($do == "baidu"){
	$wheresql .= " fid = '1'";
	$key = $baidu_array[$key];
	$title_key = ($key)?$key."_":"";
}else{
	$wheresql .= "1";
	$key = "";
	$title_key = ($key)?$key."_":"";
}

//加入KEY 
$keysql = (!empty($key) && $do != "all" && $do != "")?" AND tags like '%{$key}%' ":"";
$wheresql = $wheresql . $keysql;

if($searchkey){
	$wheresql .= " AND (subject like '%$searchkey%' OR author like '%$searchkey%')";
}
if($tags){
	$dumpurl = "live.php?tags=$tags";
	$wheresql .= " AND trim(tags)='{$tags}'";
}else{
	$dumpurl = "live.php?do=$do&key={$_GET['key']}&searchkey=$searchkey";
}



$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname("threads")." $wheresql"));
$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('threads')." $wheresql ORDER BY getdate DESC LIMIT $offer , $pagesize");
while ($value = $_SGLOBAL['db']->fetch_array($query)) {
	$value['replynum'] = $value['replies'];
	$value['viewnum']  = $value['views'];
	if($searchkey){
		$value['subject_old']  = trim(str_replace("&nbsp;"," ",$value['subject']));
		$value['subject'] = find_string(cubstr_same(trim($value['subject'])),$searchkey);
	}else{
		$value['subject_old']  = trim(str_replace("&nbsp;"," ",$value['subject']));
		$value['subject'] = cubstr_same(trim($value['subject']));
	}
	$value['date']     = $date->diff(empty($value['getdate'])?time():$value['getdate']);
	$value['tagname']  = $value['fid'];
	$value['message'] = replace_ubb_html(trim($value['message']));
	$value['message'] = ($value['fid'] == 1)?preg_replace("/\&lt\;(.*?)\&gt\;/", "",$value['message']):$value['message'];
    $value['message'] = $function->cutstr($value['message'], 200, ".....");
    $list[] = $value;
}
$multi = multi($count,$pagesize, $page, $dumpurl);
