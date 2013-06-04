<?php
/**
 * @param 内裤叔叔 分离 live.php 文件
 * @param 预览帖子
 */


if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

//将主题缓存起来
//$subject = json_decode($memcache->get(md5($tid)),true);
//if(!$subject){
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("threads")." WHERE tid='$tid'");
	$subject = $_SGLOBAL['db']->fetch_array($query);
	//$memcache->save(md5($tid), json_encode($subject),1800);
//}
if(!$subject || !trim($subject['subject'])){
	showmessage('呃，你访问的信息不存在...', "live.php");
	exit();
}
$a_tid = $a_date = array();
$c_tid = $_SCOOKIE['tid_history'];
$c_date = $_SCOOKIE['date_history'];
if($c_tid && $c_date){
	$time = time();
	$a_tid = explode("|",$c_tid);
	//除重
	array_unique($a_tid);
	if(!in_array($tid,$a_tid)){
		ssetcookie("tid_history","{$c_tid}{$tid}|",259200);
		ssetcookie("date_history","{$c_date}{$time}|",259200);
	}
}else{
	ssetcookie("tid_history",$tid."|",259200);
	ssetcookie("date_history",time()."|",259200);
}

if(!$subject['hash']){
	$return = _getDouban($subject['fromurl'],false);
	updatetable("threads",array("hash"=>$return['hash'],"head"=>$return['head'],"thispage"=>$return['thispage'],"maxpage"=>$return['maxpage']),array("tid"=>$tid));
}

if($page > 1){
	$offer = ($page - 1 ) * $pagesize;
}else{
	$offer = 0;
}

/**
 * 直播贴猜你喜欢
 */
$list_round = array();
$md5tag = md5($subject['tag']);
$list_round = $memcache->get($md5tag);
if(!$list_round){
	$sql = "SELECT COUNT(*) as num FROM ".tname("threads")." WHERE subject<>'' AND tags='{$subject['tags']}'";
	$count = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query($sql));
	if($count && $count['num'] > 0){
		$rand = mt_rand(0, $count['num'] - 1);
		$sql = "SELECT tid,subject,tags,taglist,getdate FROM ".tname("threads")." ORDER BY dateline DESC LIMIT {$rand},8";
		$query = $_SGLOBAL['db']->query($sql);
		while($value =  $_SGLOBAL['db']->fetch_array($query)){
			$value['subject'] = cubstr_same($value['subject']);
			$list_round[] = $value;
		}
		$memcache->save($md5tag, $list_round,1800);
	}
}
//下一篇
$sql = "SELECT tid,subject FROM ".tname("threads")." WHERE tid=(SELECT Min(tid) FROM ".tname("threads")." WHERE tid>{$tid})";
$nextdata = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query($sql));
if($nextdata){
	$next = "<span style=\"float:right;\"><a href=\"live.php?do=view&tid={$nextdata['tid']}\" title=\"{$nextdata['subject']}\" style=\"font-size: 13px;\">".getstr($nextdata['subject'], 50)."...</a> →</span>";
}
//上一篇
$sql = "SELECT tid,subject FROM ".tname("threads")." WHERE tid=(SELECT Max(tid) FROM ".tname("threads")." WHERE tid<{$tid})";
$updata = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query($sql));
if($updata){
	$next .= "<span style=\"margin-right: 10px;\">← <a href=\"live.php?do=view&tid={$updata['tid']}\" title=\"{$updata['subject']}\" style=\"font-size: 13px;\">".getstr($updata['subject'], 50)."...</a></span>";
}
$subject['description'] = replace_ubb_html(trim($subject['message']));
$subject['description'] = getstr($subject['message'], 120 ,0,0,0,0,1);
$subject['message'] = bbcode(str_replace("<div class=\"topic-figure cc\">","",$subject['message']),2);
if($subject['fid'] == 1){
	//替换百度里面的乱码
	$subject['message'] = preg_replace("/\&lt\;(.*?)\&gt\;/", "",$subject['message']);
}
$subject['message'] = ($subject['fid'] == 2)?str_replace(array("[url]&quot; target=[/url]","[url]&quot; target=_blank&gt;&lt;FONT SIZE=[/url]"), "",$subject['message']):$subject['message'];

//是否进行SEO匹配
if($_SCONFIG['seovalue']){
	$subject['message'] = preg_replace_callback("/({$_SCONFIG['seovalue']})/is", "replace_seo", $subject['message']);
}
$subject['h_date'] = date("Y-m-d H:i:s",$subject['dateline']);
$subject['h_getdate'] = date("Y-m-d H:i:s",$subject['getdate']);

$subject['dateline'] = $date->diff($subject['dateline']);
if($_SC['showauthor'] && $only == "lz"){
    $join = " AND p.hash = '{$subject['hash']}'";
}elseif($_SC['showauthor'] && $only == "other" &&  $other){
    $join = " AND p.hash = '{$other}'";
}else{
    $join = " AND 1";
}
//更新主表信息
updatetable("threads",array("views"=>$subject['views'] + 1),array("tid"=>$tid));

$sql = "SELECT p.pid,p.tid,p.message,p.lastpost,p.author,p.status,p.head,p.hash,p.delete,f.replies FROM ".tname('posts')." p FORCE INDEX ( hash ) LEFT JOIN ".tname("postsfield")." f ON p.pid=f.pid WHERE p.tid='{$tid}' $join ORDER BY p.pid ASC LIMIT $offer,$pagesize ";

//echo "<!--{$sql}-->";

$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT count(*) FROM ".tname("posts")." p WHERE p.tid='$tid' $join"));
//判断是否开启页面内存缓存
//缓存内页帖子
$o = ceil( $count/100 );
if( $o > 1 && $page < $o){
	if($_SCONFIG['pagememcache']){
		$list = $memcache->get("posts_{$tid}_{$page}_{$only}_{$other}");
	}else{
		$list = $filecache->get_cache("posts_{$tid}_{$page}_{$only}_{$other}");
	}
}
if(!$list){
	$query = $_SGLOBAL['db']->query($sql);
	$i = 0 ;
	while($value= $_SGLOBAL['db']->fetch_array($query)){
		if(trim($value['hash'])){
			$value['num'] = ($page - 1) * $pagesize + $i + 1;
			$value['lastpost'] = $date->diff($value['lastpost']);
			$value['description'] = preg_replace(array("/\[url\](.*?)\[\/url\]/is","/\[img\](.*?)\[\/img\]/is"), "", str_replace(array("\r\n","\r","\n"," "), "", trim($value['message'])));;
			$value['description'] = trim(getstr($value['description'], 120 ,0,0,0,0,1));
			$value['message'] = bbcode($value['message']);
			$value['message'] = ($subject['fid'] == 1)?preg_replace("/\&lt\;(.*?)\&gt\;/", "",$value['message']):$value['message'];
			$value['message'] = ($subject['fid'] == 2)?str_replace(array("[url]&quot; target=[/url]","[url]&quot; target=_blank&gt;&lt;FONT SIZE=[/url]"), "",$value['message']):$value['message'];
			$value['replies'] = is_null($value['replies'])?0:$value['replies'];
			$list[] = $value;
			$i++;
		}
	}
	if($_SCONFIG['pagememcache']){
		if( $o > 1 && $page < $o){
			$memcache->save("posts_{$tid}_{$page}_{$only}_{$other}", $list ,1800);
		}
	}else{
		if( $o > 1 && $page < $o){
			$filecache->set_cache("posts_{$tid}_{$page}_{$only}_{$other}",$list,1800);
		}
	}
	
	
}
$count_array = count($list);
$multi = multi($count,$pagesize, $page, "live.php?do=view&tid={$tid}&only={$only}&hash={$other}");



function replace_seo($matches){
	return "<a href=\"live-all-searchkey-".urlencode($matches[1]).".html\" target=\"_black\" title=\"更多关于{$matches[1]}的信息...\" style=\"text-decoration: underline;\">{$matches[1]}</a>";
}