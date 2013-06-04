<?php
header("Content-type:text/html;charset=utf-8");
set_time_limit(0);
include_once('./common.php');
include_once(S_ROOT.'./source/function_bbcode.php');
include_once(S_ROOT.'./source/function_live.php');

$filename = "./data/tieku001_hot_day.txt";
$page = isset($_GET['page'])?$_GET['page']:0;
if(is_file($filename)){
	$return = @json_decode(file_get_contents($filename),true);
}else{
	$uri = "http://www.tieku001.com/hot_day.html";
	
	$content = fopen_uri_t($uri);
	$content['content'] = iconv("gbk","utf-8",$content['content']);
	preg_match_all("/<td class=\"sjt\">(.*?)<\/td>/is", $content['content'], $cinfo);
	
	foreach($cinfo[1] as $key =>$value){
		preg_match("/href=(\"|')(.*?)\\1/is", $value ,$url);
		$data[$key] = "http://www.tieku001.com".$url[2];
	}
	
	foreach ($data as $key => $value){
		$new_content = fopen_uri_t($value);
		$new_content['content'] = iconv("gbk","utf-8",$new_content['content']);

		preg_match("/http\:\/\/bbs\.tianya\.cn\/(.*?)\.shtml/", $new_content['content'] , $tianya_uri);
		$return[$key] = $tianya_uri[0];
	}
	$return = array_unique($return);
	$fs = fopen($filename, "w+");
	fwrite($fs, json_encode($return));
	fclose($fs);
}



if($page > count($return)){
	exit("succeed!");
}

$url = $return[$page];
$nextkey = $page + 1;
$fid = 2;
$threads = $posts = array();

if(!$url && !is_array($return)){
	exit("sorry! need url");
}elseif(!$url && is_array($return)){
	echo "next";
	echo "<meta http-equiv=\"refresh\" content=\"5;URL=?page={$nextkey}\" />";
	exit();
}
echo $url."<br>";
if(!strstr($url,"bbs.tianya.cn")){
	$urla = explode("/",$url);
	$id = current(explode(".",end($urla)));
	$url = "http://bbs.tianya.cn/post-{$urla[5]}-{$id}-1.shtml";
}
if($url){
	$query = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query("SELECT * FROM ".tname("threads"). " WHERE urlhash='".md5($url)."'"));

	if(!$query){
		$array = @get_headers($url,1);
		if(preg_match('/200/',$array[0]) || preg_match('/301/',$array[0])) {
			//判断是不是 /techforum/
			if(strstr($url,"/techforum/")){
				$return = _getTianya($url);
				//判断是否为第一页开始获取？如果不是则重新获取；
				if($return && is_array($return)){
					if($return['thispage'] != 1){
						//拼凑uri
						$urilist = explode("/",$url);
						$urilist[6] = 1;
						$uri = implode("/", $urilist);
						$return = _getTianya($uri);
					}
				}
			}else{
				$return = _tianya($url);
				//需要判断是否为首页开始获取，如果不是首页需要重新获取首页；
				if($return['pagelist'] && $return['thispage'] != 0 ){
					$pagelist = explode(",",$return['pagelist']);
					$uri = str_replace($pagelist[$return['thispage']], $pagelist['0'], $return['fromurl']);
					unset($return);
					$return = array();
					$return = _tianya($uri);
				}
			}
			if($return){
				$threads['fid'] = $fid;
				$threads['author'] = mysql_real_escape_string($return['author']);
				$threads['message'] = html2bbcode($return['message']);
				$threads['subject'] = mysql_real_escape_string($return['subject']);
				$threads['dateline'] = $return['dateline'];
				$threads['views'] = $threads['replies'] = $threads['displayorder'] = 0;
				$threads['urlhash'] = $return['urlhash'];
				$threads['fromurl'] = $return['fromurl'];
				$threads['maxpage'] = $return['maxpage'];
				$threads['thispage'] = $return['thispage'];
				$threads['tags'] = $return['tags'];
				$threads['api'] = $return['api'];
				$threads['head'] = $return['head'];
				$threads['status'] = "";
				$threads['hash'] = $return['hash'];
				$threads['pagelist'] = $return['pagelist'];
				$threads['getdate'] = time();
				$tid = inserttable("threads",$threads,1);
				$taglist = Scws($threads['subject']);
				inserttag($taglist,$tid);
				$num = 0;
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
				            $pid = inserttable('posts', $posts,1);
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
				                $pid = inserttable('posts', $posts,1);
				            }
				        }
				        $num++;
				    }
				}
				$_SGLOBAL['db']->query("UPDATE ".tname("threads")." SET views='$num',replies='$num',thislouceng='{$num}' WHERE tid='$tid'");
				if($tid && $pid){
					$uid = (empty($_SGLOBAL['supe_uid']))?0:$_SGLOBAL['supe_uid'];
					$username = (empty($_SGLOBAL['supe_username']))?cplang("none"):$_SGLOBAL['supe_username'];
					$template = (empty($_SGLOBAL['supe_uid']))?str_replace("{actor}",$username.rand_name(),cplang('zhibotie')):cplang('zhibotie');
					$feedarr = array(
							'appid' => UC_APPID,
							'icon' => 'zhibotie',
							'uid' => $uid,
							'username' => $username,
							'dateline' => $_SGLOBAL['timestamp'],
							'title_template' => $template,
							'title_data' => saddslashes(serialize(sstripslashes(array('subject'=>mysql_real_escape_string(trim($return['subject'])),'tid'=>$tid,"message"=>getstr(replace_ubb_html($threads['message']), 150, 0, 1, 0, 0, -1))))),
							'body_template' => '',
							'body_data' => '',
							'id' => 0,
							'idtype' => '',
							'image_1'=>"",
					);

					$feedarr['hash_template'] = md5($feedarr['title_template']."\t".$feedarr['body_template']);//???hash
					$feedarr['hash_data'] = md5($feedarr['title_template']."\t".$feedarr['title_data']."\t".$feedarr['body_template']."\t".$feedarr['body_data']);//???hash

					inserttable('feed', $feedarr);
					echo "next";
					echo "<meta http-equiv=\"refresh\" content=\"5;URL=?page={$nextkey}\" />";
					exit();
				}else{
					echo "next";
					echo "<meta http-equiv=\"refresh\" content=\"5;URL=?page={$nextkey}\" />";
					exit();
				}
			}
		}else{
			echo "next";
			echo "<meta http-equiv=\"refresh\" content=\"5;URL=?page={$nextkey}\" />";
			exit();
		}
	}else{
		echo "next";
		echo "<meta http-equiv=\"refresh\" content=\"5;URL=?page={$nextkey}\" />";
		exit();
	}
}


function fopen_uri_t($uri){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $uri);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$data['content'] = curl_exec($ch);
	$data['header'] = curl_getinfo($ch);
	curl_close($ch);
	return $data;
}

function mb_unserialize($serial_str) {
	$serial_str= preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str );
	$serial_str= str_replace(array("\r","\\\""), array("","\""), unserialize($serial_str));
	return $serial_str;
}