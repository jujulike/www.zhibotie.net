<?php
header("Content-type:text/html;charset=utf-8");
include_once "../common.php";
include_once S_ROOT."source/function_live.php";
include_once(S_ROOT.'./source/function_bbcode.php');

$data = $_SGLOBAL["db"]->fetch_array($_SGLOBAL["db"]->query("SELECT * FROM ".tname("auto")." WHERE islock=0 LIMIT 1"));
if($data && $data["uri"]){
    switch ($data['type']){
        case "tianya":
            $url = $data["uri"];
            $query = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query("SELECT * FROM ".tname("threads"). " WHERE urlhash='".md5($url)."'"));
            updatetable("auto", array("islock"=>1), array("aid"=>$data["aid"]));
            if($query) exit("uri exists");
            
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
                    $threads['fid'] = 2;
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
                                'title_data' => saddslashes(serialize(sstripslashes(array('subject'=>saddslashes(trim($return['subject'])),'tid'=>$tid,"message"=>getstr(replace_ubb_html($threads['message']), 150, 0, 1, 0, 0, -1))))),
                                'body_template' => '',
                                'body_data' => '',
                                'id' => 0,
                                'idtype' => '',
                                'image_1'=>"",
                        );
                
                        $feedarr['hash_template'] = md5($feedarr['title_template']."\t".$feedarr['body_template']);//???hash
                        $feedarr['hash_data'] = md5($feedarr['title_template']."\t".$feedarr['title_data']."\t".$feedarr['body_template']."\t".$feedarr['body_data']);//???hash
                
                        inserttable('feed', $feedarr);
                        echo "wget succeed";
                        exit();
                    }else{
                        echo "can not wget !";
                        exit();
                    }
            }else{
                exit("uri can not access");
            }
       }
       break;
    }
    
}