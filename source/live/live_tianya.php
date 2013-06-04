<?php
/**
 * @param 内裤叔叔 分离 live.php 文件
 * @param 天涯获取
 */


if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$url = isset($_POST['url'])?$_POST['url']:"";
$fid = 2;
if(!strstr($url,"bbs.tianya.cn")){
	$urla = explode("/",$url);
	$id = current(explode(".",end($urla)));
	$url = "http://bbs.tianya.cn/post-{$urla[5]}-{$id}-1.shtml";
}
$threads = $posts = array();
if($url){
	$query = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query("SELECT * FROM ".tname("threads"). " WHERE urlhash='".md5($url)."'"));
	if(!$query){
		$array = @get_headers($url,1);
		if(preg_match('/200/',$array[0]) || preg_match('/301/',$array[0])) {
			//算地址
			$urla = explode("-",$url);
			$urlb = explode(".",$urla[3]);
			if($urlb[0] != 1){
				$urlb[0] = 1;
				$url = "{$urla[0]}-{$urla[1]}-{$urla[2]}-{$urlb[0]}.{$urlb[1]}";
			}
			$return = _tianya($url);
			if($return){
				$threads['fid'] = $fid;
				$threads['author'] = saddslashes($return['author']);
				$threads['message'] = html2bbcode($return['message']);
				$threads['subject'] = saddslashes($return['subject']);
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
				//$threads['pagelist'] = $return['pagelist'];
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
					$return['subject'] = cubstr_same(trim($return['subject']));
                    $return['message'] = replace_ubb_html(trim($return['message']));
                    $return['message'] = ($return['fid'] == 1)?preg_replace("/\&lt\;(.*?)\&gt\;/", "",$return['message']):$return['message'];
                    $return['message'] = $function->cutstr($return['message'], 200, ".....");
                    $feedarr = array(
							'appid' => UC_APPID,
							'icon' => 'zhibotie',
							'uid' => $uid,
							'username' => $username,
							'dateline' => $_SGLOBAL['timestamp'],
							'title_template' => $template,
							'title_data' => serialize(array('subject'=>saddslashes($return['subject']),'tid'=>$tid,"message"=>$return['message'])),
							'body_template' => '',
							'body_data' => '',
							'id' => 0,
							'idtype' => '',
				            'image_1'=>"",
						);

					$feedarr['hash_template'] = md5($feedarr['title_template']."\t".$feedarr['body_template']);//ϲ��hash
					$feedarr['hash_data'] = md5($feedarr['title_template']."\t".$feedarr['title_data']."\t".$feedarr['body_template']."\t".$feedarr['body_data']);//�ϲ�hash

					inserttable('feed', $feedarr);
					echo json_encode(array("status"=>1,"tid"=>$tid));
					exit();
				}else{
					echo json_encode(array("status"=>0,"tid"=>0));
					exit();
				}
			}
		}else{
			echo json_encode(array("status"=>0,"tid"=>0));
			exit();
		}
	}else{
		echo json_encode(array("status"=>0,"tid"=>$query['tid']));
		exit();
	}
}
exit();