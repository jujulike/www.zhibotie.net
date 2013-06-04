<?php
/**
 * @param 内裤叔叔 分离 live.php 文件
 * @param 更新主帖
 */


if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}
$tid = $_POST['tid'];
//$filecache->set_cache_path(S_ROOT."./data/filecache",$tid);
//echo $filecache->get_cache_path();
//获取锁
$lock_update = $memcache->get("update_{$tid}");
if($tid && !$lock_update){
	$memcache->save("update_{$tid}",1);
	$query = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query("SELECT * FROM ".tname("threads")." WHERE tid='$tid'"));
	if($query){
		$update_time = $query['update'] + 1;
		$array = @get_headers($query['fromurl'],1);
		if(preg_match('/200/',$array[0]) || preg_match('/301/',$array[0])) {
			if($query['fid'] == 1){
				$return = _baidu($query['fromurl']);
				$update = array(
						"maxpage"=>$return['maxpage'],
						"subject"=>saddslashes($return['subject' ]),
						"message"=>saddslashes($return["message"][0]),
						"hash"=>$return["hash"][0],
						"head"=>$return['head'][0],
						"author"=>saddslashes($return["author"][0]),
						"tags"=>saddslashes($return["tags"]),
						"status"=>saddslashes($return["status"]),
						"getdate"=>time(),
						"api" =>  $return['api'][0],
						"update"=>$update_time,
				);
			}elseif($query['fid'] == 0){
				$return = _getDouban($query['fromurl'],false);
				$update = array(
						"maxpage"=>$return['maxpage'],
						"subject"=>saddslashes($return['subject']),
						"message"=>saddslashes(html2bbcode($return["message"])),
						"hash"=>$return["hash"],
						"head"=>$return['head'],
						"author"=>saddslashes($return["author"]),
						"tags"=>saddslashes($return["tags"]),
						"status"=>saddslashes($return["status"]),
						"getdate"=>time(),
						"api" => $return['api'],
						"update"=>$update_time,
				);
			}elseif($query['fid'] == 2){
				$return = _tianya($query['fromurl'],false);
			 	$update = array(
						"maxpage" => $return["maxpage"],
						"author" => saddslashes($return['author']),
						"message" => saddslashes(html2bbcode($return['message'])),
						"hash" => $return['hash'],
						"head"=>$return['head'],
						"subject"=>saddslashes($return['subject']),
						"tags"=>saddslashes($return["tags"]),
						"status"=>saddslashes($return["status"]),
						"getdate"=>time(),
						"api" => $return['api'],
						"pagelist"=>$return['pagelist'],
			 			"update"=>$update_time,
			 			"fromurl"=>$return['fromurl'],
			 			"urlhash"=>$return['urlhash'],
				);
			}
			//拼凑更新历史记录表
			$threads_update = array("tid"=>$tid,
								"subject"=>saddslashes($query['subject']),
								"message"=>saddslashes($query['message']),
								"dateline"=>time(),
								);
			if($query['maxpage'] != $return['maxpage']){
				//更新主表
				updatetable("threads",$update,array("tid"=>$tid));
				//插入更新记录表
				inserttable("threads_update",$threads_update);
				if($return['hash'] != $query["hash"]){
					//更新回帖表
					updatetable("posts",array("hash"=>$update["hash"]),array("tid"=>$tid,"hash"=>$query["hash"]));
				}
				//deltreedir($filecache->get_cache_path());
				echo ajax_return(1,cplang("gengxin_succeed"),array("tid"=>$query['tid']));
			}else{
				echo ajax_return(0,str_replace("{author}",$query['author'],cplang("gengxin_author")));
			}
		}else{
			echo ajax_return(0,cplang("gengxin_delete"));
		}
	}else{
		echo ajax_return(0,cplang("gengxin_error"));
	}
	//解锁
	$memcache->delete("update_{$tid}");
}else{
	echo ajax_return(0,"need tid or update locked!");
}
exit();