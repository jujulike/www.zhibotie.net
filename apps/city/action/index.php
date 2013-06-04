<?php
// +----------------------------------------------------------------------
// | AjaxCity [ city.php ]
// +----------------------------------------------------------------------
// | Copyright (c) 2012 http://www.actphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author : Gnipt <t@php.cn> <245291359@qq.com>
// +----------------------------------------------------------------------
// | Version : 1.1 
// +----------------------------------------------------------------------
// | Update : 2012-04-19 
// +----------------------------------------------------------------------
	
	$option = isset($_GET['option'])?$_GET['option']:'';
	if(in_array($option,array('province','city','area'))){
		$modle = "get_{$option}";
		$modle();		
	}
	
	//echo get_id_by_name("湖南|长沙市|天心区",3);
	
	function echo_json($array){
		header('Content-type:text/html;charset=utf-8');
		//print_r($array);
		echo json_encode($array);	
	}
	
	function get_province(){
		$datafile = S_ROOT.'./apps/city/action/province.inc.php';
		if(file_exists($datafile)){
			$config = include($datafile);
			$provinces = array();
			foreach($config as $k=>$v){
				$province['id'] = $k;
				$province['name'] = $v;
				$provinces[] = $province;
			}		
			echo_json($provinces);	
		}			
	}
	
	function get_city(){
		$datafile = S_ROOT.'./apps/city/action/city.inc.php';
		if(file_exists($datafile)){
			$config = include($datafile);
			$province_id = get_id_by_name($_GET[pid],1);
			if($province_id != ''){
				$citylist = array();
				if(is_array($config[$province_id]) && !empty($config[$province_id])){
					$citys = $config[$province_id];
					foreach($citys as $k => $v){
						$city['id'] = $k;
						$city['name'] = $v;
						$citylist[] = $city;
					}				
				}
				echo_json($citylist);			
			}
		}		
	}
	function get_area(){
		$datafile = S_ROOT.'./apps/city/action/area.inc.php';
		if(file_exists($datafile)){
			$config = include($datafile);
			
			$province_id = get_id_by_name($_GET[pid],1);	
			$city_id = get_id_by_name("{$_GET[pid]}|{$_GET[cid]}",2);
			//echo $province_id ."|" . $city_id;
			if($province_id != '' && $city_id != ''){
				$arealist = array();				
				if(isset($config[$province_id][$city_id]) && is_array($config[$province_id][$city_id]) && !empty($config[$province_id][$city_id])){
					$areas = $config[$province_id][$city_id];
					foreach($areas as $k => $v){
						$area['id'] = $k;
						$area['name'] = $v;
						$arealist[] = $area;
					}				
				}
				echo_json($arealist);			
			}
		}		
	}
	
	/**
	 * @package 通过中文查询省份
	 */
	function get_id_by_name($string = "北京|朝阳区",$level = 1){
		//匹配//
		$str = explode("|",$string);
		if($level == 1){
			$config = include(S_ROOT.'./apps/city/action/province.inc.php');
			$return = array_keys($config,$str[0],true); 
			return $return[0];
		}elseif ($level == 2){
			$province = get_id_by_name($string,1);
			$config = include(S_ROOT.'./apps/city/action/city.inc.php');
			$return = array_keys($config[$province],trim($str[1]),true);
			return $return[0];
		}elseif($level == 3){
			$province = get_id_by_name($string,1);
			$city = get_id_by_name($string,2);
			$config = include(S_ROOT.'./apps/city/action/area.inc.php');
			$return = array_keys($config[$province][$city],trim($str[2]),true);
			return "{$province}|{$city}|{$return[0]}";
		}else{
			return "";
		}
		
	}
?>