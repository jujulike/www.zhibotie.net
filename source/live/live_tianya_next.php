<?php
/**
 * @param 内裤叔叔 分离 live.php 文件
 * @param 天涯获取下一页
 */


if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}


//接受参数
$tid = $_POST['tid'];
$maxpage = $_POST['maxpage'];
$thispage = $_POST['thispage'];
$thisnum  = $_POST['thisnum'];
$type = $_POST['type'];
$memcache = new cache_memcache();
//$memcache->clean();

$tianya_id = $memcache->get("tianya_{$tid}");

if(!$tianya_id){
	if($tid && is_numeric($tid)){
		//查询数据库
		$rest = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query("SELECT * FROM ".tname("threads")." WHERE tid = '{$tid}'"));

		//是否有下一页
		$nextpage = $rest['thispage'] + 1;
		if($rest && $nextpage){
			if($maxpage == $thispage &&  $maxpage == $rest['maxpage']){
				//最大页数 大于等于当前页数，并且楼层数为98
				echo "it's maxpage";
			}elseif($maxpage > $thispage && $maxpage == $rest['maxpage']) {
				//设置锁定
				$memcache->save("tianya_{$tid}", $tid);
				//最大页数 大于 当前页数 ，并且楼层数为98
				//获取下一页
				//是否是/techforum/
// 				if(strstr($rest['fromurl'],"/techforum/")){
// 					$urilist = explode("/",$rest['fromurl']);
// 					if(strstr($urilist[6],".shtml")){
// 						$urilist[7] = $urilist[6];
// 						$urilist[6] = $rest['thispage'] + 1;
// 					}else{
// 						$urilist[6] = $rest['thispage'] + 1;
// 					}
// 					$url = implode("/", $urilist);
// 					$return = _getTianya($url);
// 				}else{
// 					$url = str_replace($pagelist[0], $pagelist[$thispage + 1], $rest['fromurl']);
// 					$return = _tianya($url);
// 				}
				$urla = explode("-",$rest['fromurl']);
				$urlb = explode(".",$urla[3]);
				$url = "{$urla[0]}-{$urla[1]}-{$urla[2]}-{$nextpage}.{$urlb[1]}";
				$return = _tianya($url,false);
				$num = 0;
				echo $url;
				if($return){
					foreach($return["reply"]["message"] as $key => $v){
					    if($v[ 'hash' ]){
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
        						if($rest['hash'] ==  $return["reply"]["hash"][$key]){
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
					//更新主表记录；
					if($pid && $tid && $rest){
						updatetable("threads",array("thispage" => $nextpage,"thislouceng" => $num ,'replies'=>$rest['replies'] + $num,'views'=>$rest['views']+$num), array("tid"=>$tid));
					}
					$memcache->delete("tianya_{$tid}");
					echo ajax_return(1,"next page get succeed");
				}
			}elseif($maxpage < $thispage){
				echo "maxpage < thispage";
			}elseif($maxpage >= $thispage && $maxpage == $rest['maxpage']){
				//获取当前页
				echo ajax_return(0,"没有下一页了~~；");
			}
		}else{
			echo ajax_return(0,"tid error OR en. 100");
		}
	}else{
		echo ajax_return(0,"time out!!!");
	}
}else{
	echo "lock! someone used";
}
exit();