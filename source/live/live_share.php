<?php
/**
 * @param 内裤叔叔 分离 live.php 文件
 * @param 分享
 */


if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

include_once(S_ROOT.'./source/function_cp.php');
$arr = array();
$tid = $_POST['tid'];
$content = js_unescape($_POST['content']);
$query = $_SGLOBAL['db']->query("SELECT * FROM  ".tname("threads")." WHERE tid='$tid'");
if(!$threads = $_SGLOBAL['db']->fetch_array($query)){
	echo ajax_return(0,cplang("no_zhibotie_threads"));
	exit();
}
$arr['title_template'] = cplang('share_zhibotie');
$arr['body_template'] = '<b>{subject}</b><br>{username}<br>{message}<div class="quote\"><span class="q">{content}</span></div>';
$arr['body_data'] = array(
		'subject' => "<a href=\"live.php?do=view&tid={$threads[tid]}\">$threads[subject]</a>",
		'username' => "<a href=\"javascript:void(0);\">".$threads['author']."</a>",
		'message'=>  getstr(preg_replace(array("/\[url\](.*?)\[\/url\]/is","/\[img\](.*?)\[\/img\]/is"), "", str_replace(array("\r\n","\r","\n"," "), "", cubstr_same(trim($threads['message'])))), 150, 0, 1, 0, 0, -1),
		'content'=> $content,
);
preg_match("#\[img\](.*?)\[\/img\]#",$threads['message'],$src);
if($src){
	$uri = "http://www.zhibotie.net/".str_replace("&amp;", "&", $src[1]);
	$link = @get_headers($uri,TRUE);
	$arr['image'] = $link['Location']."!small";
	$arr['image_link'] = "/live.php?do=view&tid={$threads[tid]}";
}
$arr['body_general'] = getstr(saddslashes($content), 150, 1, 1, 1, 1);
$arr['type'] = "zhibotie";
	$arr['uid'] = $_SGLOBAL['supe_uid'];
	$arr['username'] = $_SGLOBAL['supe_username'];
	$arr['dateline'] = $_SGLOBAL['timestamp'];
	$arr['topicid'] = $_POST['topicid'];
	$arr['body_data'] = serialize($arr['body_data']);//����ת��
	//剔除反斜杠编译
$setarr = saddslashes($arr);//����ת��

$sid = inserttable('share', $setarr, 1);
    updatestat('share');
//更新分享数
if(empty($space['sharenum'])) {
$space['sharenum'] = getcount('share', array('uid'=>$space['uid']));
$sharenumsql = "sharenum=".$space['sharenum'];
	} else {
	$sharenumsql = 'sharenum=sharenum+1';
}
$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET {$sharenumsql}, lastpost='$_SGLOBAL[timestamp]', updatetime='$_SGLOBAL[timestamp]' WHERE uid='$_SGLOBAL[supe_uid]'");
//分享
include_once(S_ROOT.'./source/function_feed.php');
feed_publish($sid, 'sid', 1);

// 	//获取新浪短网址  将 长网址 变为 新浪短网址
// 	$uri = sina_short_url("925507530","http://www.zhibotie.net/live-view-tid-{$tid}-only-author.html");
// 	@include_once(S_ROOT."./apps/oauth/action/oauthConnect.php");
// 	$oauth = new oauthConnect();
// 	if($_POST['douban_send']){
// 	$query = $_SGLOBAL['db']->query("SELECT * from ".tname("oauth") ." WHERE uid='{$_SGLOBAL['supe_uid']}' AND `type`='douban'");
// 	$douban_n = $_SGLOBAL['db']->fetch_array($query);
// 	//平凑标题、正文
// 	$message_douban = "推荐直播贴：".$theards['subject']."   {$uri}";
// 	$oauth->add_new_weibo('douban',$message_douban,$douban_n['token'],$douban_n['token_secret']);
// 	}

	echo ajax_return(1,cplang("share_zhibotie_succeed"));
	exit();
