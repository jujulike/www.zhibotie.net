<?php
    /**
     *@param 直播贴
     *@Id:   space_zhibotie.php
     *@version 0.4
     *@param 0.4版本加入天涯获取通道，修复页面错位等问题
     *		 0.5版本加入ID加密功能；
     *		 0.6版本修复豆瓣无法获取回帖的情况
     *		 0.7版本修复重复获贴等情况
     *       0.8版本修复获取豆瓣/天涯/百度等图片
     *       0.9版本修复只获取楼主言论
     *       0.9.1版本加入SWCS分词，将threads subject 分词
     *       0.9.2版本修复天涯帖1子重复的情况
     *       0.9.3版本修复豆瓣、百度、天涯获取下一页重复的情况，加入memcache锁定概念
     *       0.9.4版本修复天涯获取时间为0或者时间超过当前时间43年的情况
     *       0.9.5版本加入天涯板块【莲蓬鬼话】获取
     *       0.9.6版本分离各类处理动作为单独文件
     *       0.9.7版本加入浏览历史，同时支持分页
     */		 

header("Content-type:text/html;charset=utf-8");
include_once('./common.php');
include_once(S_ROOT.'./source/function_bbcode.php');
include_once(S_ROOT.'./source/function_live.php');
include_once(S_ROOT.'./source/class.cache.php');
include_once(S_ROOT.'./source/functionInit.php');

//检测站点是否关闭
checkclose();

//处理rewrite
if($_SCONFIG['allowrewrite'] && isset($_GET['rewrite'])) {
	$rws = explode('-', $_GET['rewrite']);
	if($rw_uid = intval($rws[0])) {
		$_GET['uid'] = $rw_uid;
	} else {
		$_GET['do'] = $rws[0];
	}
	if(isset($rws[1])) {
    	$rw_count = count($rws);
		for ($rw_i=1; $rw_i<$rw_count; $rw_i=$rw_i+2) {
			$_GET[$rws[$rw_i]] = empty($rws[$rw_i+1])?'':$rws[$rw_i+1];
		}
	}
	unset($_GET['rewrite']);
}

$memcache = new cache_memcache();
$filecache = new filecacheInit();
$function = new functionInit();

//获取DO
$do = isset($_GET['do'])?$_GET['do']:"all";
//允许的动作
if(!in_array($do,array("all","view","douban","tianya","baidu","mymark","history","hot","getNew","getNew_tianya","getNew_baidu","commont","zhibotie_share","search","get_baidu","get_tianya","gengxin_subject","pcommont","mark","thread","deletemymark","tianyatianya","doubandouban"))){
	showmessage('未授权的操作，请返回！', geturl()."live.php");
}

//处理参数
$author = isset($_GET['author'])?$_GET['author']:"";
$page = isset($_GET['page'])?$_GET['page']:1; //PAGE参数用于翻页
$only = isset($_GET['only'])?$_GET['only']:"lz"; //是否只看楼主
$only = $only == "author" ? "lz" : $only;
$only_name = $only == "lz" ? "查看所有" : "只看楼主";
$only_link = $only == "lz" ? "all" : "lz";
$other = isset($_GET['hash'])?$_GET['hash']:"";
$pagesize = (int)100;
if($page > 1){
    $offer = ($page - 1 ) * $pagesize;
}else{
    $offer = 0;
}
$searchkey = isset($_GET['searchkey'])?$_GET['searchkey']:"";
$tid       = isset($_GET['tid'])?$_GET['tid']:0;
//$tid       = is_numeric($tid)?$tid:$cry->php_decrypt($tid);
if(!is_nume($tid) && $tid){
	showmessage('一个非法ID，请返回！', "live.php");
}
$uid       = $_SGLOBAL['supe_uid'];
$tags      = isset($_GET['tags'])?$_GET['tags']:"";
/*
if($tid){
	//根据 tid 获取分表表名
	$post_table = $_SGLOBAL['db']->get_hash_table("posts",$tid);
}
*/

//设置缓存路径
$filecache->set_cache_path(S_ROOT."./data/filecache",$tid);
//获取浏览记录
$cookies = unserialize(stripslashes($_SCOOKIE['live_history']));
$history_count = count($cookies);

$subject = array();
ssetcookie("live_history","",0);
$toplist = toplist(12);


//获取下一页新帖 douban
if( $do == 'getNew' )
{
	include_once(S_ROOT.'./source/live/live_douban_next.php');
}

//获取天涯下一页新帖
if($do == "getNew_tianya"){
	include_once(S_ROOT.'./source/live/live_tianya_next.php');
}

//获取百度下一页新帖
if($do == "getNew_baidu"){
	include_once(S_ROOT.'./source/live/live_baidu_next.php');
}

//评论
if($do == "commont"){
	include_once(S_ROOT.'./source/live/live_commont.php');
}

//分享
if($do == "zhibotie_share"){
	include_once(S_ROOT.'./source/live/live_share.php');
}


//获取豆瓣
if($do == "search"){
	include_once(S_ROOT.'./source/live/live_douban.php');
}


//获取百度
if($do == "get_baidu"){
	include_once(S_ROOT.'./source/live/live_baidu.php');
}


//获取天涯
if($do == "get_tianya"){
	include_once(S_ROOT.'./source/live/live_tianya.php');
}

//获取天涯
if($do == "tianyatianya"){
    print_r($_SC);
    //print_r(fopen_url($_GET['uri']));
	$return = _tianya($_GET['uri']);
	
	foreach($return["reply"]["message"] as $key => $v){
	    if($return["reply"]["hash"][$key]){
	        if($_SC['getothercommont']){
	            $posts['hash'] = $return["reply"]["hash"][$key];
	            $posts['subject'] = saddslashes($return['subject']);
	            $posts['message'] = html2bbcode($v);
	            $posts['author'] = saddslashes($return["reply"]["author"][$key]);
	            $posts['fid'] = $fid;
	            $posts['tid'] = $tid;
	            $posts['frist'] = 0;
	            $posts['head'] = $return["reply"]["head"][$key];
	            $posts['status'] = "";
	            $posts['lastpost'] = $return["reply"]["lastpost"][$key];
	            //$pid = inserttable('posts', $posts,1);
	            print_r($posts);
	        }
	        else{
	            if($return['hash'] ==  $return["reply"]["hash"][$key]){
	                $posts['hash'] = $return["reply"]["hash"][$key];
	                $posts['subject'] = saddslashes($return['subject']);
	                $posts['message'] = html2bbcode($v);
	                $posts['author'] = saddslashes($return["reply"]["author"][$key]);
	                $posts['fid'] = $fid;
	                $posts['tid'] = $tid;
	                $posts['frist'] = 0;
	                $posts['head'] = $return["reply"]["head"][$key];
	                $posts['status'] = "";
	                $posts['lastpost'] = $return["reply"]["lastpost"][$key];
	                //$pid = inserttable('posts', $posts,1);
	                print_r($posts);
	            }
	        }
	        $num++;
	    }
	}
}

//获取天涯
if($do == "doubandouban"){
	print_r(_getDouban($_GET['uri']));
}


//更新主帖
if($do == "gengxin_subject"){
	include_once(S_ROOT.'./source/live/live_update.php');
}

include_once(S_ROOT."source/function_date.php");
$date = new dateService();

if($do == "hot"){
	include_once(S_ROOT.'./source/live/live_hot.php');
}

//MARK记录
if($do == "mark"){
	include_once(S_ROOT.'./source/live/live_mark.php');
}

//我的MARK记录
if($do == "mymark"){
	include_once(S_ROOT.'./source/live/live_my_mark.php');
}

//我的浏览记录
if($do == "history"){
	@include_once(S_ROOT.'./source/live/live_history.php');
}

//删除我的mark;
if($do == "deletemymark"){
	include_once(S_ROOT.'./source/live/live_deletemymark.php');
}


/**
 * 获取页面缓存信息
 * 主帖展示
 */
if($tid && $do == 'view'){
	$threadcache = getcache($tid,$page);
	
	$_SGLOBAL['threadcache'] = $threadcache;
	if($_SCONFIG['pagecache'] && is_file($threadcache['filename']) && $threadcache['filemtime'] && ($_SGLOBAL['timestamp'] - $threadcache['filemtime'] < $_SCONFIG['pagecachetime'])){
		echo @sreadfile($threadcache['filename']);
		echo "<!--cached on {$threadcache['filemtime']} path:{$threadcache['filename']} time: ".($_SCONFIG['debuginfo']?debuginfo():"")."-->";
		exit();
	}else{
		@unlink($threadcache['filename']);
		include_once(S_ROOT.'./source/live/live_view.php');
		$_SGLOBAL['threadcache']['tid'] = $tid;
		$_SGLOBAL['threadcache']['page'] = $page;
		$_SGLOBAL['threadcache']['count'] = $count;
		include_once template("live");
		exit();
	}
}

//列表
elseif ($do == "all" || $do == "douban" || $do == "tianya" || $do == "thread" || $do == "baidu"){
	include_once(S_ROOT.'./source/live/live_list.php');
}



include_once template("live");


unset($list,$filecache);
unset($mylist);


/**
 * 获取热门帖子
 * @param int $num
 * @return array();
 */
function toplist($num){
	global $memcache,$_SGLOBAL;
	$toplist = $memcache->get("toplist");
	if(!$toplist){
		$sql = "SELECT tid,replies,views,subject,getdate FROM ".tname("threads")." WHERE (replies / views ) * 100 >= 50 ORDER BY replies DESC LIMIT {$num}";
		$query = $_SGLOBAL['db']->query($sql);
		while($value = $_SGLOBAL['db']->fetch_array($query)){
			$toplist[] = $value;
		}
		$memcache->save("toplist", $toplist , 86400);
	}
	return $toplist;
}

