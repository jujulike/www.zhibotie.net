<?php
/**
 * @param 内裤叔叔 分离 live.php 文件
 * @param 百度获取下一页
 */


if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}


//获取参数
$tid = $_POST['tid'];
$maxpage = $_POST['maxpage'];
$thispage = $_POST['thispage'];

//判断5分钟内是否已获取？
$memcache = new cache_memcache();
$baidu_id = $memcache->get("baidu_{$tid}");
if(!$baidu_id){
	if($tid && is_numeric($tid)){
		//查询数据记录
		$rest = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query("SELECT * FROM ".tname("threads")." WHERE tid = '{$tid}'"));
		if($rest){
			//判断最大数是否相同;
			if($maxpage == $thispage && $maxpage == $rest['maxpage']){
				//最大页数 大于等于当前页数，并且楼层数为三十
				echo "it's maxpage";
				exit();
			}elseif($maxpage > $thispage && $maxpage == $rest['maxpage']) {
				//最大页数 大于 当前页数 ，并且楼层数为三十
				//获取下一页
				$url = "{$rest['fromurl']}?pn=".intval($thispage + 1);
				$array = _baidu($url);
				$num = 0;
				if(count($array['message'] >= 30)){
					if($array){
						foreach($array['author'] as $key => $value){
						    if($_SC['getothercommont']){
						        $datas['author'] = saddslashes($value);
						        $datas['tid'] = $tid;
						        $datas['fid'] = $type;
						        $datas['subject'] = saddslashes($rest['subject']);
						        $datas['message'] = saddslashes($array['message'][$key]);
						        $datas['author'] = saddslashes($array['author'][$key]);
						        $datas['lastpost'] = $array['dateline'][$key];
						        $datas['status'] = "";
						        $datas['head'] = $array['head'][$key];
						        $datas['hash'] = $array['hash'][$key];
						        $datas['frist'] = 0;
						        $pid = inserttable("posts", $datas ,1 );
						    }else{
    							if($rest['hash'] == $array['hash'][$key]){
    								$datas['author'] = $value;
    								$datas['tid'] = $tid;
    								$datas['fid'] = $type;
    								$datas['subject'] = saddslashes($rest['subject']);
						            $datas['message'] = saddslashes($array['message'][$key]);
						            $datas['author'] = saddslashes($array['author'][$key]);
    								$datas['lastpost'] = $array['dateline'][$key];
    								$datas['status'] = "";
    								$datas['head'] = $array['head'][$key];
    								$datas['hash'] = $array['hash'][$key];
    								$datas['frist'] = 0;
    								$pid = inserttable("posts", $datas ,1 );
    							}
						    }
							$num++;
						}
						//更新主表记录；
						if($pid && $tid && $rest){
							updatetable("threads",array("thispage" => $thispage + 1,"thislouceng" => $num ,'replies'=>$rest['replies'] + $num,'views'=>$rest['views']+$num), array("tid"=>$tid));
						}
						echo ajax_return(1);
					}
				}else{
					echo 'Enough 100';
				}
			}elseif($maxpage < $thispage){
				echo "maxpage < thispage";
			}
		}else{
			echo "tid error";
		}
	}else{
		echo "time out !";
	}
	$memcache->save("baidu_{$tid}",$tid,300);
}else{
	echo "lock! someone used.";
}
exit();