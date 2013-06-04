<?php

/*
 *@param 瀑布流
 */

$option = $_GET['option'];
$option = empty($option)?"index":$option;

$page   = isset($_GET['page'])?intval($_GET['page']):1;
include_once S_ROOT.'./source/function_class_image.php';
$image = new imageInit();

switch ($option){
	case "index":
		$pagesize = 20;
		$offer = get_pages($page,$pagesize);
		$sql = "SELECT a.id as ids,a.name,a.desc,p.id,p.path,p.thumb FROM ".tname("albums")." a LEFT JOIN ".tname("photos")." p ON a.cover_id=p.id ORDER BY up_time DESC LIMIT $offer,$pagesize";
		//获取动态信息
		$sql_sql = "SELECT * from ".tname("feed")." where icon='zhibotie' OR (icon='doing' and id<>'0') ORDER BY dateline LIMIT $offer,$pagesize";
		$query1 = $_SGLOBAL['db']->query($sql_sql);
		while($value = $_SGLOBAL['db']->fetch_array($query1)){
			$value = mkfeed($value);
			$datas['title'] = ($value['title_data']["subject"] == "")?$value['username']:$value['title_data']["subject"];
			$datas['desc'] = $value['title_data']['message'];
			//if(!file_exists($value['image_1'].".200X200.jpg") && file_exists($value['image_1']) && $value['image_1'] != ""){
			//	$image->make_thumb($value['image_1'],"{$value['image_1']}.200X200",200,200,true);
				
			//}
			$datas['image'] = $value['image_1'];//.".200X200.jpg";		
			$datas['id'] = ($value['id'] == 0)?$value['title_data']["tid"]:$value['id'];
			$datas['ids'] = $datas['id'];
			$list_a[] = $datas;
		}
		unset($datas);
		unset($query1);
		$query = $_SGLOBAL['db']->query($sql);	
		while($value = $_SGLOBAL['db']->fetch_array($query)){
					$datas['title'] = $value['name'];
					$datas['desc'] = $value['desc'];
					$datas['image'] = "{$value['path']}.thumb.jpg";
					$datas['id'] = $value['id'];
					$datas['ids'] = $value['ids'];
					$list_b[] = $datas;
		}
		//合并；
		$list = array_merge($list_b,$list_a);
		//print_r($list);
		include template("apps/push/{$apps_config[tpl]}index");;
		break;
	case "view":
		//$pagesize = 3;
		//$offer = get_pages($page,$pagesize);
		$id = (int)mysql_real_escape_string($_GET['id']);
		if($id){
			$subject = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query("SELECT * FROM " . tname("albums")." where id='{$id}'"));
			if($subject){
				$subject['subject'] = $subject['name'];
				//echo "SELECT * FROM ".tname("photos")." WHERE album_id='{$id}' ORDER BY create_time DESC LIMIT {$offer},{$pagesize}";
				$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("photos")." WHERE album_id='{$id}' ORDER BY create_time DESC ");//LIMIT {$offer},{$pagesize}");
				while($value = $_SGLOBAL['db']->fetch_array($query)){
					
					$list[] = $value;
				}
			}
		}
		include template("apps/push/{$apps_config[tpl]}view");;
		break;
	default:;
}


function get_pages($page = 1,$pagesize = 20){
	//$pagesize = (int)$pagesize;
	if($page > 1){
	    $offer = ($page - 1 ) * $pagesize;
	}else{
	    $offer = 0;
	}
	return $offer;
}