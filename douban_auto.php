<?php

header("Content-type:text/html;charset=utf-8");
include_once('./common.php');
include_once(S_ROOT.'./source/function_bbcode.php');
include_once(S_ROOT.'./source/function_live.php');

$option = isset($_GET['option'])?$_GET['option']:"douban";
$filename = "./data/getLZ.{$option}.txt";
if($option == "tianya"){
	$uri = "http://www.onlylz.com/zhibo/%E5%A4%A9%E6%B6%AF%E7%A4%BE%E5%8C%BA.html";
	$key = isset($_GET['key'])?$_GET['key']:0;
	$nextkey = $key + 1;
	$content = @file_get_contents($filename);
	if(!$content){
		$content = fopen_url($uri);
		preg_match_all("/<li class=\"typost\">(.*?)<\/li>/is", $content, $daitl);
		foreach ($daitl[1] as $key => $value){
			preg_match("/href=\"http:\/\/www\.onlylz\.com\/postcache\/(.*?)\/page\.html/is", $value , $uri);
			$url = "http://www.onlylz.com/postcache/{$uri[1]}/page.html";
			$newcontent = fopen_url($url);
			preg_match("/<li>分类：(.*?)<\/li>/is", $newcontent, $newuri);
			preg_match("/href=\"http:\/\/www.tianya.cn\/(.*?)\"/is", $newuri[1] , $uri);
			$urilist[] = "http://www.tianya.cn/{$uri[1]}";
		}
		print_r($urilist);
		$fs = fopen($filename, "w");
		fwrite($fs, serialize($urilist));
		fclose($fs);
		$content = $urilist;
	}else{
		$content = unserialize($content);
	}
	
	if($key > count($content)){
		exit("succeed!");
	}
	$url = $content[$key];
	$fid = 2;
	$threads = $posts = array();
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
					$threads['head'] = $threads['status'] = "";
					$threads['hash'] = $return['hash'];
					$threads['pagelist'] = $return['pagelist'];
					$threads['getdate'] = time();
					$tid = inserttable("threads",$threads,1);
					$taglist = Scws($threads['subject']);
					inserttag($taglist,$tid);
					$num = 0;
				    foreach($return['reply']  as $key => $v){
                        if($v["hash"]){
                            if($_SC['getothercommont']){
                                $newlist['hash'] = $v['hash'];
                                $newlist['subject'] = $return['subject'];
                                $newlist['message'] = html2bbcode($v['message']);
                                $newlist['author'] = saddslashes($v['author']);
                                $newlist['fid'] = 1;
                                $newlist['tid'] = $tid;
                                $newlist['frist'] = 0;
                                $newlist['head'] = $v['head'];
                                $newlist['status'] = saddslashes($v['status']);;
                                $newlist['lastpost'] = $v['lastpost'];
                                $pid = inserttable('posts', $newlist,1);
                                $last[] = $newlist;
                            }else{
                                if($return['hash'] == $v['hash']){
                                	$newlist['hash'] = $v['hash'];
                                    $newlist['subject'] = $return['subject'];
                                    $newlist['message'] = html2bbcode($v['message']);
                                    $newlist['author'] = saddslashes($v['author']);
                                    $newlist['fid'] = 1;
                                    $newlist['tid'] = $tid;
                                    $newlist['frist'] = 0;
                                    $newlist['head'] = $v['head'];
                                    $newlist['status'] = saddslashes($v['status']);;
                                    $newlist['lastpost'] = $v['lastpost'];
                                    $pid = inserttable('posts', $newlist,1);
                                    $last[] = $newlist;
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
								'title_data' => saddslashes(serialize(sstripslashes(array('subject'=>mysql_real_escape_string(trim($return['subject'])),'tid'=>$tid,"message"=>getstr(preg_replace(array("/\[url\](.*?)\[\/url\]/is","/\[img\](.*?)\[\/img\]/is"), "", str_replace(array("\r\n","\r","\n"," "), "", cubstr_same(trim(html2bbcode($return['message']))))), 150, 0, 1, 0, 0, -1))))),
								'body_template' => '',
								'body_data' => '',
								'id' => 0,
								'idtype' => '',
								'image_1'=>"",
						);
	
						$feedarr['hash_template'] = md5($feedarr['title_template']."\t".$feedarr['body_template']);//ϲ��hash
						$feedarr['hash_data'] = md5($feedarr['title_template']."\t".$feedarr['title_data']."\t".$feedarr['body_template']."\t".$feedarr['body_data']);//�ϲ�hash
	
						inserttable('feed', $feedarr);
						echo "next";
					echo "<meta http-equiv=\"refresh\" content=\"5;URL=?option={$option}&key={$nextkey}\" />";
					exit();
					}else{
						echo "next";
					echo "<meta http-equiv=\"refresh\" content=\"5;URL=?option={$option}&key={$nextkey}\" />";
					exit();
					}
				}
			}else{
				echo "next";
					echo "<meta http-equiv=\"refresh\" content=\"5;URL=?option={$option}&key={$nextkey}\" />";
					exit();
			}
		}else{
			echo "next";
					echo "<meta http-equiv=\"refresh\" content=\"5;URL=?option={$option}&key={$nextkey}\" />";
					exit();
		}
	}
}elseif($option == "douban"){
	$uri = "http://www.zhiboju.com/";
	$key = isset($_GET['key'])?$_GET['key']:0;
	$nextkey = $key + 1;
	$content = @file_get_contents($filename);
	if(!$content){
		$content = fopen_url($uri);
		$content = $content['content'];
		preg_match_all("/<h3>(.*?)<\/h3>/is", $content, $title);

        foreach($title[1] as $key =>$value){
            preg_match("/href=\"(.*?)\"/is",$value,$url);
            if($url){
            	$uri = "http://www.zhiboju.com/{$url[1]}";
            	$content = fopen_url($uri);
            	$content = $content['content'];
            	preg_match("/原帖:(.*?)<\/div>/is", $content,$u);
            	$urilist[] = trim($u[1]);
            }
        }
		$fs = fopen($filename, "w");
		fwrite($fs, serialize($urilist));
		fclose($fs);
		$content = $urilist;
	}else{
		$content = unserialize($content);
	}
	
	if($key > count($content)){
		echo "succeed!";
		echo "<meta http-equiv=\"refresh\" content=\"5;URL=?option={$option}&key={$nextkey}\" />";
		exit();
	}
	$url = $content[$key];
	$fid = 0;
	$threads = $posts = array();
	if($url){
		if(strexists($url,"#")){
			$urlarray = explode("#",$url);
			$url = $urlarray[0];
		}
		$urls = explode("?",$url);
		$url = $urls[0];
		$tid = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query("SELECT * FROM ".tname("threads"). " WHERE urlhash='".md5($url)."'"));
		if(!$tid){
			$array = @get_headers($url,1);
			if(preg_match('/200/',$array[0]) || preg_match('/301/',$array[0])) {
				$return = _getDouban($url);
				$threads['fid'] = $fid;
				$threads['author'] = mysql_real_escape_string($return['author']);
				$threads['dateline'] = $return['dateline'];
				$threads['subject'] = mysql_real_escape_string(trim($return['subject']));
				$threads['message'] = html2bbcode($return['message']);
				$threads['views'] = $threads['replies'] = $threads['displayorder'] = 0;
				$threads['urlhash'] = $return['urlhash'];
				$threads['fromurl'] = $return['fromurl'];
				$threads['maxpage'] = $return['maxpage'];
				$threads['thispage'] = $return['thispage'];
				$threads['tags']     = $return['tag'];
				$threads['head'] = $return['head'];
				$threads['hash'] = $return['hash'];
				$threads['thislouceng'] = 0;
				$threads['getdate'] = time();
				$threads['api'] = $return['api'];
				$tid = inserttable("threads",$threads,1);
				//开始通过标题分词
				$taglist = Scws($threads['subject']);
				inserttag($taglist,$tid);
				$num = 0;
				foreach($return['reply']  as $key => $v){
				    if($_SC['getothercommont']){
				        $newlist['hash'] = $v['hash'];
				        $newlist['subject'] = $return['subject'];
				        $newlist['message'] = html2bbcode($v['message']);
				        $newlist['author'] = mysql_real_escape_string($v['author']);
				        $newlist['fid'] = 1;
				        $newlist['tid'] = $tid;
				        $newlist['frist'] = 0;
				        $newlist['head'] = $v['head'];
				        $newlist['status'] = mysql_real_escape_string($v['status']);;
				        $newlist['lastpost'] = $v['lastpost'];
				        $pid = inserttable('posts', $newlist,1);
				        $last[] = $newlist;
				    }else{
    					if($return['hash'] == $v['hash']){
    						$newlist['hash'] = $v['hash'];
    						$newlist['subject'] = $return['subject'];
    						$newlist['message'] = html2bbcode($v['message']);
    						$newlist['author'] = mysql_real_escape_string($v['author']);
    						$newlist['fid'] = 1;
    						$newlist['tid'] = $tid;
    						$newlist['frist'] = 0;
    						$newlist['head'] = $v['head'];
    						$newlist['status'] = mysql_real_escape_string($v['status']);;
    						$newlist['lastpost'] = $v['lastpost'];
    						$pid = inserttable('posts', $newlist,1);
    						$last[] = $newlist;
    					}
				    }
					$num++;
				}
				//$pid = insert_more(array("subject","message","author","fid","tid","frist","hash","head","status","lastpost"),$last,"posts");
				if($tid && $pid){
					updatetable("threads",array("views"=>$num,"replies"=>$num,"thislouceng"=>$num),array("tid"=>$tid),1);
					//分享开始
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
							'title_data' => saddslashes(serialize(sstripslashes(array('subject'=>mysql_real_escape_string(trim($return['subject'])),'tid'=>$tid,"message"=>getstr(preg_replace(array("/\[url\](.*?)\[\/url\]/is","/\[img\](.*?)\[\/img\]/is"), "", str_replace(array("\r\n","\r","\n"," "), "", cubstr_same(trim(html2bbcode($return['message']))))), 150, 0, 1, 0, 0, -1))))),
							'body_template' => '',
							'body_data' => '',
							'id' => 0,
							'idtype' => '',
							'image_1'=>"",
					);
	
					$feedarr['hash_template'] = md5($feedarr['title_template']."\t".$feedarr['body_template']);//ϲ��hash
					$feedarr['hash_data'] = md5($feedarr['title_template']."\t".$feedarr['title_data']."\t".$feedarr['body_template']."\t".$feedarr['body_data']);//�ϲ�hash
					inserttable('feed', $feedarr);
					echo "next";
					echo "<meta http-equiv=\"refresh\" content=\"5;URL=?option={$option}&key={$nextkey}\" />";
					exit();
				}else{
					echo "next";
					echo "<meta http-equiv=\"refresh\" content=\"5;URL=?option={$option}&key={$nextkey}\" />";
					exit();
				}
			}else{
				echo "next";
				echo "<meta http-equiv=\"refresh\" content=\"5;URL=?option={$option}&key={$nextkey}\" />";
				exit();
			}
			 
		}else{
			echo "next";
			echo "<meta http-equiv=\"refresh\" content=\"5;URL=?option={$option}&key={$nextkey}\" />";
			exit();
		}
	}else{
		echo "succeed!";
		exit();
	}
}
