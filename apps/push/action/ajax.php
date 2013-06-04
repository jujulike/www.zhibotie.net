<?php
//ajax操作；

// 实际应用中 data 一般从数据库读取

$option = $_GET['option'];
$option = empty($option)?"index":$option;
$page   = isset($_GET['page'])?intval($_GET['page']):1;
$pagesize = (int)50;
if($page > 1){
    $offer = ($page - 1 ) * $pagesize;
}else{
    $offer = 0;
}

switch ($option){
	case "index":
		$sql = "SELECT a.*,p.id,p.path,p.thumb FROM ".tname("albums")." a LEFT JOIN ".tname("photos")." p ON a.cover_id=p.id ORDER BY up_time DESC LIMIT $offer,$pagesize";
		$query = $_SGLOBAL['db']->query($sql);	
		while($value = $_SGLOBAL['db']->fetch_array($query)){
					$datas['title'] = $value['name'];
					$datas['desc'] = $value['desc'];
					$datas['image'] = "{$value['path']}.thumb.jpg";
					$list[] = $datas;
		}
		echo json_encode($list);
		break;
	default:;
}