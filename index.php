<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: index.php 13003 2009-08-05 06:46:06Z liguode $
*/

include_once('./common.php');

// //进行来源判断，如果是IE 并且有$URI那么进行录入
// $from = $_GET['from'];
// $uri = $_GET['uri'];
// if($from && $uri){
// 	//进行数据查询
// 	$md5uri = md5($uri);
// 	$result = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query("SELECT tid,urlhash FROM ".tname("threads")." WHERE urlhash='{$md5uri}'"));
// 	print_r($result);
// 	if($result){
// 		header("Location:live-view-tid-{$result['tid']}.html");
// 		exit();
// 	}else{
// 		//进行fid判断
// 		$isIE = true;
// 		if(strstr($uri,"douban.com/group/topic/")){
// 			$fid = 0;
// 			$do_view = "search";
// 		}elseif(strstr($uri,"tianya.cn/publicforum/content/") || strstr($uri,"tianya.cn/techforum/content/")){
// 			$fid = 2;
// 			$do_view = "get_tianya";
// 		}elseif(strstr($uri,"tieba.baidu.com/p/") || strstr($uri,"tieba.baidu.com/f?kz")){
// 			$fid = 1;
// 			$do_view = "get_baidu";
// 			$uri = strstr($uri,"f?kz=")?str_replace("f?kz=", "p/", $uri):$uri;
// 		}
// 	}	
// }


//�жϿͻ��˷���
$User_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
//echo $User_agent;
//���Ϊandroid iphone ucweb ��ֱ����ת;
if(strexists($User_agent,"android") || strexists($User_agent,"iphone") || strexists($User_agent,"ucweb") || strexists($User_agent,"windows phone") ){
    header("Location:http://m.zhibotie.net/");
    exit();
}

if(is_numeric($_SERVER['QUERY_STRING'])) {
	showmessage('enter_the_space', "space.php?uid=$_SERVER[QUERY_STRING]", 0);
}


//��������
if(!isset($_GET['do']) && $_SCONFIG['allowdomain']) {
	$hostarr = explode('.', $_SERVER['HTTP_HOST']);
	$domainrootarr = explode('.', $_SCONFIG['domainroot']);
	if(count($hostarr) > 2 && count($hostarr) > count($domainrootarr) && $hostarr[0] != 'www' && !isholddomain($hostarr[0])) {
		showmessage('enter_the_space', $_SCONFIG['siteallurl'].'space.php?domain='.$hostarr[0], 0);
	}
}


if($_SGLOBAL['supe_uid']) {
	//跳转到空间去
	showmessage('enter_the_space', 'space.php?do=home', 0);
}

//内裤叔叔 2012.08.16 删除 广场~~
/*
if(empty($_SCONFIG['networkpublic'])) {
*/
	//加载核心类
	include_once(S_ROOT.'./source/functionInit.php');
	include_once(S_ROOT.'./source/function_bbcode.php');
	$function = new functionInit();
 
    //首页开始
    $index_hot = $index_list = array();
    $memcache = new cache_memcache();

	$index_hot = $memcache->get("index_hot");
	if(!$index_hot){
		$sql = "select * from ".tname("threads")." ORDER BY (replies*2+views) DESC,getdate DESC LIMIT 0,10";	
        $query = $_SGLOBAL['db']->query($sql);
		while($value = $_SGLOBAL['db']->fetch_array($query)){
            $value['oldtags'] = $value['tags'];
            $value['tags'] = cubstr_same($value['tags']);
            $value['subject'] = cubstr_same(trim($value['subject']));
            $index_hot[] = $value;
		}
        $memcache->save("index_hot",$index_hot,1800);
	}
	$indexhot = $index_hot;
	
	$topiclist = $newlist = array();
	$topiclist = $memcache->get("topiclist_index");
	if(!$topiclist){
		$sql = "SELECT * FROM ".tname("threads")." WHERE displayorder='1' ORDER BY tid DESC LIMIT 0,12";
		$query = $_SGLOBAL['db']->query($sql);
		    while($value = $_SGLOBAL['db']->fetch_array($query)){
		    	$value['message'] = replace_ubb_html(trim($value['message']));
		    	$value['message'] = ($value['fid'] == 1)?preg_replace("/\&lt\;(.*?)\&gt\;/", "",$value['message']):$value['message'];
		    	$value['title'] = $value['subject'];
		    	$value['subject'] = cubstr_same(trim($value['subject']));
		    	$value['message'] = $function->cutstr($value['message'], 200, ".....");
		    	$value['oldtags'] = $value['tags'];
		    	$value['tags'] = cubstr_same($value['tags']);
		    	$value['hotimg'] = strstr($value['hotimg'],".thumb.jpg")?$value['hotimg']:str_replace("!big", "!jian.140", $value['hotimg']);
		    	$value['dateline'] = str_replace("-", ".", date("Y-m-d",$value['dateline']));
		    	$topiclist[] = $value;
		    }
		    $memcache->save("topiclist_index", $topiclist , 1800);
	}
	
// 	if($_SGLOBAL['supe_uid']){
// 		include_once(S_ROOT."source/function_date.php");
// 		$date = new dateService();
// 		$feed = array();
// 		$feed = $memcache->get("index_feed_list");
// 		$feed = json_decode($feed,TRUE);
// 		if(!$feed){
// 			$sql = "SELECT * FROM ".tname("feed")." WHERE icon='doing' ORDER BY dateline DESC LIMIT 0,5";
// 			$query = $_SGLOBAL['db']->query($sql);
// 			while($value = $_SGLOBAL['db']->fetch_array($query)){
// 				$value = mkfeed($value);
// 				$value['dateline'] = $date->diff($value['dateline']);
// 				$feed[] = $value;
// 			}
// 			$memcache->save("index_feed_list", json_encode($feed) , "1800");
// 		}
// 	}
	
	include_once template("index");
	unset($memcache);
?>