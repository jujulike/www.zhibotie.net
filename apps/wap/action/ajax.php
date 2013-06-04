<?php 
/**
 * @param 内裤叔叔 手机客户端ajax提交数据
 */

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$option = strtolower($_GET['option']);

if($option){
    switch($option){
        case "comment":
        	$tid = mysql_real_escape_string($_POST['tid']);
            $message = getstr($_POST['content'], 0, 1, 1, 1, 2);
            $message = html2bbcode($message);
            $subject = mysql_real_escape_string($_POST['subject']);
            $array = array("tid"=>$tid,"subject"=>$subject,"fid"=>1,"message"=>$message,"author"=>"手机游客","lastpost"=>time(),"status"=>"","head"=>"","hash"=>md5($message));
            $pid = inserttable("posts", $array,1);
            if($pid){
            	
            }
            break;
        case "mymark":
            //判断是否是游客，是否已登录
            $tid = $_GET['tid'];
            $pid = $_GET['pid'];
            $num = $_GET['num'];
            if($_SGLOBAL['supe_uid']){
                //是注册用户，而且已经登录
            }elseif ($tid &&$pid && $num){
                //未登录
                //保存数组 一定要 转义！！！！
                $array = unserialize(stripslashes($_SCOOKIE['mymark']));
                $array[$tid]['pid'] = $pid;
                $array[$tid]['dateline'] = $_SGLOBAL['timestamp'];
                $array[$tid]['num'] = $num;
                ssetcookie("mymark",serialize($array),86400 * 365);
                exit(ajax_return(1,"ok"));
            }
            break;
        default:;
    }
}else{}

function round_name(){
    
}