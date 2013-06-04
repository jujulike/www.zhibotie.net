<?php
/**
 * @param 每一个小时同步一条新闻到新浪微博
 * @author 内裤叔叔
 * @example 需要在linux下设置 cron job ;
 */
header("Content-type:text/html;charset=utf-8");
include_once('./common.php');
include_once(S_ROOT.'./source/function_bbcode.php');

//将$key 存入 memcached中


//开始定义一个必须的缓存文件，用于储存最新发送过后的帖子;
$filename = "./data/weibo_cache.php";
$data = @file_get_contents($filename);
$data = json_decode($data,true);
$time = time();
if(!$data){
	$sql = "SELECT * FROM ".tname("threads")." ORDER BY tid ASC LIMIT 1";
	$query = $_SGLOBAL['db']->query($sql);
	$data = $_SGLOBAL['db']->fetch_array($query);
	$data['sendtime'] = $time;
	if($data){
		$fs = fopen($filename, "w");
		fwrite($fs, json_encode($data));
		fclose($fs);
	}else{
		runlog('send_weibo', "error_code: no data",1);
		exit();
	}
}elseif($time - $data['sendtime'] > 1500){
	$sql = "SELECT * FROM ".tname("threads")." WHERE tid >{$data['tid']} ORDER BY tid ASC LIMIT 1";
	$query = $_SGLOBAL['db']->query($sql);
	$data = $_SGLOBAL['db']->fetch_array($query);
	$data['sendtime'] = $time;
	if($data){
		$fs = fopen($filename, "w");
		fwrite($fs, json_encode($data));
		fclose($fs);
	}
	else{
		runlog('send_weibo', "error_code: no data",1);
		exit();
	}
}else{
	$data = array();
	runlog('send_weibo', "error_code: timeout",1);
	exit();
}

if($data){
	include_once(S_ROOT."./apps/oauth/inc/sina.class.php");
	//每次进行发布，需要查询数据库，毁三观的ID，获取sina的授权token
	$uid = '101423';
	$sql = "SELECT * FROM ".tname("oauth")." WHERE uid='101423' AND type='sina' LIMIT 1";
	$user = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query($sql));
	if($user && $user['token']){
		//正则匹配一张图
		preg_match("/\[img\](.*?)\[\/img\]/is", $data['message'] , $image);
		//开始处理content
		$data['message'] = replace_ubb_html($data['message']);
		$content = "【".trim($data['subject'])."】". getstr($data['message'], 130 ,0,0,0,0,1)."... @{$data['author']}  http://www.zhibotie.net/live-view-tid-{$data['tid']}.html";
		$sina = new sinaClass();
		//判断是否是图片微博
		if($data['displayorder'] == 1 && $data['hotimg']){
			//开始平凑图片地址
			$path = (strstr($data['hotimg'], "http://image.zhibotie.net") || strstr($data['hotimg'], "http://www-zhibotie-net.b0.upaiyun.com")) ?$data['hotimg']:"http://www.zhibotie.net/{$data['hotimg']}";
			$return = $sina->update_img($user['token'], $content, $path);
		}else{
			if($image){
				$path = "http://www.zhibotie.net/".$image[1];
// 				if(strstr($image[1],"thumbp.php")){
// 					$path_link = @get_headers("http://www.zhibotie.net/{$image[1]}",true);
// 					if($_SCONFIG['allowftp']){
// 						$path = $_SCONFIG['ftpurl'].$path_link['Location'];
// 					}else{
// 						$path = "http://www.zhibotie.net/".$path_link['Location'];
// 					}
// 				}else{
// 					$path = "http://www.zhibotie.net/".$image[1];
// 				}
				$return = $sina->update_img($user['token'], $content, $path);
			}else{
				$return = $sina->update($user['token'], $content);
			}
		}
		if($return['error_code']){
			runlog('send_weibo', "tid: {$data['tid']} send failed. error_code: ". json_encode($return));
		}else{
			runlog('send_weibo', "tid: {$data['tid']} send succeed. sina_id: {$return['id']}");
		}
	}else{
		runlog('send_weibo', "error_code: token error.");
	}
}