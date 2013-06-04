<?php
/**
 * @param 内裤叔叔 分离 live.php 文件
 * @param 百度获取
 */


if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}


$url = $_POST['url'];
    $fid = isset($_POST['fid'])?$_POST['fid']:1;
    $threads = $posts = array();
    if($url){
        $urls = explode("?",$url);
        $url = $urls[0];
        $tid = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query("SELECT * FROM ".tname("threads"). " WHERE urlhash='".md5($url)."'"));
        if(!$tid){
            if(check_url_is_ok($url)){
                $return = _baidu($url);
                $threads['subject'] =  saddslashes($return['subject']);
                $threads['author'] = saddslashes($return['author'][0]);
                $threads['api'] = $return['api'][0];
                $threads['fid'] = $fid;
                $threads['dateline'] = $return['dateline'][0];
                $threads['displayorder'] = 0;
                $threads['urlhash'] = $return['urlhash'];
                $threads['fromurl'] = $return['fromurl'];
                $threads['maxpage'] = $return['maxpage'];
                $threads['thispage'] = $return['thispage'];
                $threads['tags'] = $return['tags'];
                $threads['head'] = $return['head'][0];
                $threads['status'] = $threads['pagelist'] = "";
                $threads['getdate'] = time();
                $threads['thislouceng'] = 0;
                $threads['hash'] = $return['hash'][0];
                $threads['message'] = $return['message'][0];
                $tid = inserttable("threads",$threads,1);
                $taglist = Scws($threads['subject']);
                inserttag($taglist,$tid);
                for($i=1;$i<=count($return['message']);$i++){
                    if($return['hash'][$i]){
                        if($_SC['getothercommont']){
                            $posts['hash'] = $return['hash'][$i];
                            $posts['subject'] = saddslashes($return['subject']);
                            $posts['message'] = $return['message'][$i];
                            $posts['author'] = saddslashes($return['author'][$i]);
                            $posts['fid'] = $fid;
                            $posts['tid'] = $tid;
                            $posts['frist'] = 0;
                            $posts['head'] = $return['head'][$i];
                            $posts['status'] = "";
                            $posts['lastpost'] = $return['dateline'][$i];
                            $pid = inserttable('posts', $posts,1);
                            $last[] = $posts;
                        }else{
                            if($return['hash'][0] == $return['hash'][$i]){
                                $posts['hash'] = $return['hash'][$i];
                                $posts['subject'] = saddslashes($return['subject']);
                                $posts['message'] = $return['message'][$i];
                                $posts['author'] = saddslashes($return['author'][$i]);
                                $posts['fid'] = $fid;
                                $posts['tid'] = $tid;
                                $posts['frist'] = 0;
                                $posts['head'] = $return['head'][$i];
                                $posts['status'] = "";
                                $posts['lastpost'] = $return['dateline'][$i];
                                $pid = inserttable('posts', $posts,1);
                                $last[] = $posts;
                            }
                        }
                        $num++;
                    }
                }
                if($tid && $pid){
                    echo json_encode(array("status"=>1,"tid"=>$tid));
                    updatetable("threads",array("views"=>$num,"replies"=>$num,"thislouceng"=>$num),array("tid"=>$tid),1);
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
                    exit();
                }
            }
            else{
                echo json_encode(array("status"=>0,"tid"=>0));
                exit();
            }
        }
        else{
            echo json_encode(array("status"=>0,"tid"=>$tid['tid']));
            exit();
        }
    }
    
