<?php

//用户名片设置

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

session_start();

$uid = $_GET['id'];
if($uid && strstr($uid, "||")){
	$list = explode("||", $uid);
	$api = $list[0];
	$type = $list[1];
	if($type == "0"){
		include_once(S_ROOT."./source/function_class_image.php");
		$function = new imageInit();
		$datapath = avatar_path($api);
		$path = "./data/json_cache/".$datapath['path'];

		$function->createFolder($path);
		$filename = "./data/json_cache/".$datapath['file']."_{$api}.json";
		if(file_exists($filename)){
			$json = sreadfile($filename);
		}else{
			include_once(S_ROOT."./apps/oauth/action/oauthConnect.php");
			$connect = new oauthConnect();
			$data = $connect->getUserInfo($api, "douban");
			swritefile($filename,$data);
			$json = $data;
		}
		$json = json_decode($json,TRUE);
		$json['head'] = $json['link']['2']["@href"];
		$json['username'] = $json['title']['$t'];
		$json['api'] = $api;
		$json['note'] = str_replace(array("\r\n","\r","\n"), "", $json['content']['$t']);
		$json['resideprovince'] = $json['db:location']['$t'];
		include template("apps/{$m}/{$apps_config[tpl]}douban");
	}
}elseif(is_numeric($uid) && $uid && $_SGLOBAL['supe_uid']){
    //获取用户信息
    $query = $_SGLOBAL['db']->query("SELECT s.*,f.* from ".tname("space")." s left join ".tname("spacefield")." f ON s.uid=f.uid WHERE s.uid='{$uid}'");
    $userlist = $_SGLOBAL['db']->fetch_array($query);
    $query = $_SGLOBAL['db']->query("SELECT * from ".tname("friend")." WHERE uid='{$_SGLOBAL['supe_uid']}' and fuid='{$uid}'");
    $isfriend = $_SGLOBAL['db']->fetch_array($query);
    include template("apps/{$m}/{$apps_config[tpl]}{$a}");
}else{
   $string = <<<EOT
<div class="bm_hover_card" style="top: 211px; left: 555.5px;">
    <div class="bm_hover_card_arrow"></div>
    <div class="bm_hover_card_border"></div>
    <div class="bm_hover_card_container">
    	<div class="bm_hover_card_before">系统错误....</div>
    </div>
</div>   
EOT;
	echo $string;
	exit();
}