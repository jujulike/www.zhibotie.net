<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: common.php 13217 2009-08-21 06:57:53Z liguode $
*/

@define('IN_UCHOME', TRUE);
define('D_BUG', '0');

D_BUG?error_reporting(7):error_reporting(0);
set_magic_quotes_runtime(0);
//提高memcached命中率
ini_set("memcache.hash_strategy", "consistent");

$_SGLOBAL = $_SCONFIG = $_SBLOCK = $_TPL = $_SCOOKIE = $_SN = $space = array();

//程序目录
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);

//基本文件
include_once(S_ROOT.'./ver.php');
if(!@include_once(S_ROOT.'./config.php')) {
	header("Location: install/index.php");//安装
	exit();
}
include_once(S_ROOT.'./source/function_common.php');

//时间
$mtime = explode(' ', microtime());
$_SGLOBAL['timestamp'] = $mtime[1];
$_SGLOBAL['supe_starttime'] = $_SGLOBAL['timestamp'] + $mtime[0];

//GPC过滤
$magic_quote = get_magic_quotes_gpc();
if(empty($magic_quote)) {
	$_GET = saddslashes($_GET);
	$_POST = saddslashes($_POST);
}

//本站URL
if(empty($_SC['siteurl'])) $_SC['siteurl'] = getsiteurl();

//链接数据库
dbconnect();

//缓存文件
if(!@include_once(S_ROOT.'./data/data_config.php')) {
	include_once(S_ROOT.'./source/function_cache.php');
	config_cache();
	include_once(S_ROOT.'./data/data_config.php');
}
foreach (array('app', 'userapp', 'ad', 'magic') as $value) {
	@include_once(S_ROOT.'./data/data_'.$value.'.php');
}

//COOKIE
$prelength = strlen($_SC['cookiepre']);
foreach($_COOKIE as $key => $val) {
	if(substr($key, 0, $prelength) == $_SC['cookiepre']) {
		$_SCOOKIE[(substr($key, $prelength))] = empty($magic_quote) ? saddslashes($val) : $val;
	}
}

//启用GIP
if ($_SC['gzipcompress'] && function_exists('ob_gzhandler')) {
	ob_start('ob_gzhandler');
} else {
	ob_start();
}

//初始化
$_SGLOBAL['supe_uid'] = 0;
$_SGLOBAL['supe_username'] = '';
$_SGLOBAL['inajax'] = empty($_GET['inajax'])?0:intval($_GET['inajax']);
$_SGLOBAL['mobile'] = empty($_GET['mobile'])?'':trim($_GET['mobile']);
$_SGLOBAL['ajaxmenuid'] = empty($_GET['ajaxmenuid'])?'':$_GET['ajaxmenuid'];
$_SGLOBAL['refer'] = empty($_SERVER['HTTP_REFERER'])?'':$_SERVER['HTTP_REFERER'];
if(empty($_GET['m_timestamp']) || $_SGLOBAL['mobile'] != md5($_GET['m_timestamp']."\t".$_SCONFIG['sitekey'])) $_SGLOBAL['mobile'] = '';

//登录注册防灌水机
if(empty($_SCONFIG['login_action'])) $_SCONFIG['login_action'] = md5('login'.md5($_SCONFIG['sitekey']));
if(empty($_SCONFIG['register_action'])) $_SCONFIG['register_action'] = md5('register'.md5($_SCONFIG['sitekey']));

//整站风格
if(empty($_SCONFIG['template'])) {
	$_SCONFIG['template'] = 'default';
}
if($_SCOOKIE['mytemplate']) {
	$_SCOOKIE['mytemplate'] = str_replace('.','',trim($_SCOOKIE['mytemplate']));
	if(file_exists(S_ROOT.'./template/'.$_SCOOKIE['mytemplate'].'/style.css')) {
		$_SCONFIG['template'] = $_SCOOKIE['mytemplate'];
	} else {
		ssetcookie('mytemplate', '', 365000);
	}
}

//处理REQUEST_URI
if(!isset($_SERVER['REQUEST_URI'])) {  
	$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'];
	if(isset($_SERVER['QUERY_STRING'])) $_SERVER['REQUEST_URI'] .= '?'.$_SERVER['QUERY_STRING'];
}
if($_SERVER['REQUEST_URI']) {
	$temp = urldecode($_SERVER['REQUEST_URI']);
	if(strexists($temp, '<') || strexists($temp, '"')) {
		$_GET = shtmlspecialchars($_GET);//XSS
	}
}

qqconnect();
	
//判断用户登录状态
checkauth();
$_SGLOBAL['uhash'] = md5($_SGLOBAL['supe_uid']."\t".substr($_SGLOBAL['timestamp'], 0, 6));

//用户菜单
getuserapp();

//处理UC应用
$_SCONFIG['uc_status'] = 0;
$_SGLOBAL['appmenus'] = $_SGLOBAL['appmenu'] = array();
if($_SGLOBAL['app']) {
	foreach ($_SGLOBAL['app'] as $appid => $value) {
		if(UC_APPID != $appid) {
			$_SCONFIG['uc_status'] = 1;
		}
		if($value['open']) {
			if(empty($_SGLOBAL['appmenu'])) {
				$_SGLOBAL['appmenu'] = $value;
			} else {
				$_SGLOBAL['appmenus'][] = $value;
			}
		}
	}
}

//加载memcached
include_once(S_ROOT."./source/class.memcache.php");

if($_SGLOBAL['supe_uid']){
    require_once S_ROOT.'./apps/oauth/action/check.php';
}

//通用
$douban_array = array("asshole"=>"我总觉得自己就是一个傻逼","zhuangb"=>"文艺青年装逼会","taotaopaoxiao"=>"景涛同好组！！！！！！","blabla"=>"八卦来了",
		"JPpeople"=>"我们身边的皇家极品直播中！",
		"tomorrow"=>"灵异豆瓣 ",
		"lingyi"=>"真实的灵异经历",
		"gay"=>"本少要给自己找BF",
		"cultwomen"=>"不靠谱女青年联盟",
		"lvguangsenlin"=>"装逼指南",
		"160105"=>"打垮小三",
		"g"=>"鬼故事",
		"insidestory"=>"掀起你的内幕来",
        "doubanzhibo"=>"豆瓣直播-当时我就直播了",
        'spoil'=>"我们什么都知道一点儿"
);
$tianya_array = array("guihua"=>"莲蓬鬼话","818"=>"娱乐八卦","feel"=>"情感天地","icon"=>"时尚资讯","zatan"=>"天涯杂谈",
			"gayles"=>"一路同行",
		"lvy"=>"旅游休闲",
		"world"=>"国际观察",
		"history"=>"煮酒论史"
		
);
$baidu_array = array("wow"=>"魔兽世界","ds"=>"李毅","gay8"=>"gay","jiaoguan"=>"教官","fun"=>"娱乐");

//统计数量
//每五分钟统计一次
$memcached = new cache_memcache();
list($doubancount,$tianyacount,$baiducount) = $memcached->get("zhibotie_count");
if(!$doubancount || !$tianyacount || !$baiducount){
	$zhibotie[] = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname("threads")." WHERE fid=0"));
	$zhibotie[] = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname("threads")." WHERE fid=2"));
	$zhibotie[] = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname("threads")." WHERE fid=1"));
	list($doubancount,$tianyacount,$baiducount) = $zhibotie;
	$memcached->save("zhibotie_count", $zhibotie , 300);
}
// $doubancount = $doubancount > 1000 ? "999+" : $doubancount;
// $tianyacount = $tianyacount > 1000 ? "999+" : $tianyacount;
// $baiducount = $baiducount > 1000 ? "999+" : $baiducount;
unset($memcached);


?>