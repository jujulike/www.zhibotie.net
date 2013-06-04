<?php
/**
 * @param 内裤叔叔 分离 live.php 文件
 * @param 豆瓣获取下一页
 */


if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$url = $_POST['url'];
    $fid = isset($_POST['fid'])?$_POST['fid']:0;
    $theards = $posts = array();
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
                    $threads['author'] = saddslashes($return['author']);
                    $threads['dateline'] = $return['dateline'];
                    $threads['subject'] = saddslashes(trim($return['subject']));                   
                    $threads['message'] = html2bbcode($return['message']);
                    $threads['views'] = $threads['replies'] = $threads['displayorder'] = 0;
                    $threads['urlhash'] = $return['urlhash'];
                    $threads['fromurl'] = $return['fromurl'];
                    $threads['maxpage'] = $return['maxpage'];
                    $threads['thispage'] = $return['thispage'];
                    $threads['tags']     = $return['tags'];
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
                    //$pid = insert_more(array("subject","message","author","fid","tid","frist","hash","head","status","lastpost"),$last,"posts");
                    if($tid && $pid){
                        echo json_encode(array("status"=>1,"tid"=>$tid));
                        updatetable("threads",array("views"=>$num,"replies"=>$num,"thislouceng"=>$num),array("tid"=>$tid),1);
						//分享开始
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
                    }else{
                        echo json_encode(array("status"=>0,"tid"=>0));
                        exit();
                    }
               }else{
                 echo json_encode(array("status"=>0,"tid"=>0));
                 exit();
               }
               
        }else{
            echo json_encode(array("status"=>0,"tid"=>$tid['tid']));
            exit();
        }
    }