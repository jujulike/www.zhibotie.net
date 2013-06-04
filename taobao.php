<?php
/***
 * 直播贴增值服务淘宝客
 */

header("Content-type:text/html;charset=utf-8");
include_once('./common.php');
include_once(S_ROOT.'./api/taobao/TaoHooks.class.php');
include_once(S_ROOT."./source/function_taobao.php");
include_once(S_ROOT."./source/class.Crypt.php");
$crypt = new SysCrypt();


//处理rewrite
if($_SCONFIG['allowrewrite'] && isset($_GET['rewrite'])) {
	$rws = explode('-', $_GET['rewrite']);
	if($rw_uid = intval($rws[0])) {
		$_GET['uid'] = $rw_uid;
	} else {
		$_GET['do'] = $rws[0];
	}
	if(isset($rws[1])) {
    	$rw_count = count($rws);
		for ($rw_i=1; $rw_i<$rw_count; $rw_i=$rw_i+2) {
			$_GET[$rws[$rw_i]] = empty($rws[$rw_i+1])?'':$rws[$rw_i+1];
		}
	}
	unset($_GET['rewrite']);
}


//接受参数
$option = isset($_GET['option'])?trim(strtolower($_GET['option'])):"list";
//实例化淘宝类
$taobao = new TaoApi();
$uid    = $_SGLOBAL['supe_uid'];

$uri = $_GET['uri'];
if($uri && $_GET['action'] == "test"){
	preg_match("/[\?&]id=(.*?)&/is", $uri,$id);
	if(!$id){
		preg_match("/[\?&]id=(.*)/is", $uri,$id);
	}
	$sid = $id[1];
	
}

if($option == "getlist"){
	$pagesize = 50;
	$page = isset($_GET['page'])?$_GET['page']:1;
	if($page > 1){
	    $offer = ($page - 1 ) * $pagesize;
	}else{
	    $offer = 0;
	}
	
	$sql = "SELECT t.*,tf.* FROM ".tname("taobao")." LIMIT {$offer},{$pagesize}";
	$query = $_SGLOBAL['db']->query($sql);
	while($value = $_SGLOBAL['db']->fetch_array($query)){
		$value['localpic'] = $value['localpic']."!big";
		$value['like'] = ($value['like'])?$value['like']:0;
		$list[] = $value;
	}
	exit(ajax_return(1,"ok",$list));
}elseif($option == "list"){
	$pagesize = 50;
	$page = isset($_GET['page'])?$_GET['page']:1;
	if($page > 1){
	    $offer = ($page - 1 ) * $pagesize;
	}else{
	    $offer = 0;
	}
	$tags = $_GET['tags'];
	
	if($tags){
	//查询标签
		$query = $_SGLOBAL['db']->query("SELECT taoid FROM ".tname("taobao_tags_filed")." WHERE tagid = (SELECT tagid FROM ".tname("taobao_tags")." WHERE `name`='{$tags}')");
		while($value = $_SGLOBAL['db']->fetch_array($query)){
			$re[] = $value['taoid'];
		}
		$joinsql = implode(",",$re);
		$wheresql = " id IN ({$joinsql}) ";
	}else{
		$wheresql = "1";
	}
	
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("taobao_tags")." ORDER BY `count` DESC LIMIT 0,12");
	while($value = $_SGLOBAL['db']->fetch_array($query)){
		$value['urlname'] = urlencode($value['name']);
		$taglist[] = $value;
	}
	
	$sql = "SELECT * FROM ".tname("taobao")." WHERE {$wheresql} ORDER BY dateline DESC LIMIT {$offer},{$pagesize}";
	$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT count(*) FROM ".tname("taobao")." WHERE {$wheresql}"));
	$query = $_SGLOBAL['db']->query($sql);
	while($value = $_SGLOBAL['db']->fetch_array($query)){
		if(!strstr($value['localpic'],"!big"))	$value['localpic'] = str_replace("!middle.160", "!big", $value['localpic']);
		//$value['taokeurl'] = "taobao.php?option=goto&gotouri=".urlencode($value['taokeurl']);//$crypt->php_encrypt($value['taokeurl']);
		$list[] = $value;
	}
	
	$multi = multi($count,$pagesize, $page, "taobao.php?option=list&tags={$tags}");
	
	include_once template("taobao");
}elseif ($option == "get"){
	if($_SGLOBAL['supe_uid']){
		$uri = isset($_POST['taoid'])?$_POST['taoid']:0;
		if($uri){
			$data = $taobao->searchshop($uri);
			$data['scws'] = Scws_array($data[t]);
			foreach ($data['scws']['words'] as $key =>$v){
				$scws[] = $v['word'];
			}
			$scws = array_unique($scws);
			
			if(is_array($data) && $data[s] == 1){
				//开始插入数据
				$taoarray = array();
				$taoarray['pic'] = $data["pic"];
				$taoarray['localpic'] = taobao_upyun($data["pic"])."!middle.160";
				list($taoarray["w"],$taoarray["h"],$tp) = @getimagesize(str_replace("!middle.160", "!big", $taoarray['localpic']));
				$taoarray['html'] = $data['html'];
				$taoarray['taoid'] = $uri;
				$taoarray['subject'] = $data['t'];
				$taoarray['price'] = $data['p'];
				$taoarray['nick'] = $data['nick'];
				$taoarray['uid'] = $_SGLOBAL['supe_uid'];
				$taoarray['dateline'] = time();
				$taoarray['ip'] = getonlineip(1);
				$taoarray['display'] = 0;
				$taoarray['tags'] = str_replace("@", ",", implode("@", $scws));
				$taoarray['taokeurl'] = $data['taokeurl'];
				$taoarray['url'] = $data['url'];
				$data['taoid'] = $taoid = inserttable("taobao", $taoarray ,1);
				//插入标签
				foreach ($scws as $key => $v){
					$sring = trim($v);
					if(strlen($sring) != 3 && !is_numeric($sring)){
						$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("taobao_tags")." WHERE name='{$sring}'");
						$tagslist = $_SGLOBAL['db']->fetch_array($query);
						if($tagslist){
							updatetable("taobao_tags", array("count"=>intval($tagslist['count'] + 1)), array("tagid"=>$tagslist['tagid']));
							//更新
							inserttable("taobao_tags_filed", array("taoid"=>$taoid,"tagid"=>$tagslist['tagid']));
						}else{
							$instags = array("name"=>$sring,"sort"=>0,"ishot"=>0,"count"=>1);
							$tagid = inserttable("taobao_tags", $instags,1);
							inserttable("taobao_tags_filed", array("taoid"=>$taoid,"tagid"=>$tagid));
						}
					}
				}
			}
			exit(ajax_return(1,"succeed",$data));
		}else{
			exit(ajax_return(0,"需要ID"));
		}
	}else{
		exit(ajax_return(0,"请先登录"));
	}
}elseif ($option == "delete"){
	$hash = $_GET['hash'];
	$taoid = $_GET['id'];
	if($hash == formhash() && is_numeric($taoid) && $taoid){
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("taobao")." WHERE id='{$taoid}'");
		$data = $_SGLOBAL['db']->fetch_array($query);
		if($data){
			delete_upyun($data['localpic']);
			$_SGLOBAL['db']->query("DELETE  FROM ".tname("taobao_filed")." WHERE taoid='{$taoid}'");
			$_SGLOBAL['db']->query("DELETE  FROM ".tname("taobao")." WHERE id='{$taoid}'");
		}
		exit(ajax_return(1,"ok"));
	}else{
		exit(ajax_return(0,"请勿非法操作"));
	}
}elseif($option == "ilike"){
	$taoid = isset($_GET['taoid'])?$_GET['taoid']:0;
	$hash  = $_GET['hash'];
	if($taoid && $hash == formhash()){
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("taobao_like")." WHERE taoid='{$taoid}' AND uid='{$uid}'");
		$data = $_SGLOBAL['db']->fetch_array($query);
		if($data){
			exit(ajax_return(0,"您已喜欢过"));
		}else{
			$query = $_SGLOBAL['db']->query("SELECT f.*,t.like FROM ".tname("taobao_filed")." f LEFT JOIN ".tname("taobao")." t ON t.id=f.taoid WHERE f.taoid='{$taoid}'");
			$rest = $_SGLOBAL['db']->fetch_array($query);
			if(!$rest){
				inserttable("taobao_filed", array("taoid"=>$taoid,"filed"=>"like","message"=>"{$uid},","dateline"=>time()));
			}else{
				updatetable("taobao_filed", array("message"=>$rest['message']."{$uid},"), array("taoid"=>$taoid));
			}
			$setarray = array("taoid"=>$taoid,"uid"=>$uid);
			$l = inserttable("taobao_like",$setarray,1 );
			$_SGLOBAL['db']->query("UPDATE ".tname("taobao")." SET `like`=`like`+1 WHERE id={$taoid}");
			exit(ajax_return(1,"",array("count"=>intval($rest['like'] + 1))));
		}		
	}else{
		exit(ajax_return(0,"非法操作"));
	}
		
}elseif($option == "taobaoke"){
    if($_GET[action] == "post"){
        $pid = $_GET['pid'];
        $nid = $_GET['nid'];
        if($pid && $nid){
            $data = $taobao->searchshop_by_pid($nid,$pid);
            if(is_array($data) && $data[s] == 1){
                $taoarray = array();
				$taoarray['pic'] = $data["pic"];
				$taoarray['localpic'] = $data["pic"];
				$taoarray['html'] = $data['html'];
				$taoarray['taoid'] = $nid;
				$taoarray['subject'] = $data['t'];
				$taoarray['price'] = $data['p'];
				$taoarray['nick'] = $data['nick'];
				$taoarray['uid'] = $_SGLOBAL['supe_uid'];
				$taoarray['taokeurl'] = $data['taokeurl'];
				$taoarray['url'] = $data['url'];
				$taoarray['commission_rate']  = $data["commission_rate"];
				$taoarray['commission'] = $data["commission"];
                echo ajax_return(1,"ok",$taoarray);
                exit();
            }
        }else{
            echo ajax_return(0,"ok");
            exit();
        }
    }
    include_once template("taobaoke");
}elseif($option == "goto"){
	$uri = $_GET['gotouri'];
	if($uri){
		$string = urldecode($uri);//$crypt->php_decrypt($uri);
		if($string){
			header("Location:{$string}");
			exit();
		}else{
			exit("uri error");
		}
	}
}