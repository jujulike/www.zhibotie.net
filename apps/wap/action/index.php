<?php

/**
 * @author 内裤叔叔
 * @param wap版本手机访问首页
 * @example apps.php?m=wap&a=index
 */
$U = $_SERVER['HTTP_USER_AGENT'];

$option = isset($_GET['option'])?$_GET['option']:"index";

include_once(S_ROOT."source/function_date.php");
include_once(S_ROOT.'./source/function_bbcode.php');
$date = new dateService();
$time = date("m-d H:i",$_SGLOBAL['timestamp']);
switch ($option){
	
	case "index":
		//判断是否登录
        $time = date("m-d H:i",$_SGLOBAL['timestamp']);
        $page = isset($_GET['page'])?$_GET['page']:1; //PAGE参数用于翻页
        $pagesize = (int)20;
        if($page > 1){
            $offer = ($page - 1 ) * $pagesize;
        }else{
            $offer = 0;
        }
        //获取分类
        $fid = isset($_GET['fid'])?$_GET['fid']:"all";
        $fid = ($fid == "all")?"":$fid;
        header("location:http://m.zhibotie.net/index_fid_{$fid}");
        exit();
        $tags = $_GET['tags'];
        if($fid != "all"){
            $wheresql = " fid='{$fid}'";
        }else{
            $wheresql = "1";
        }
        if($tags){
            $wheresql .= " AND tags='{$tags}'";
        }else{
            $wheresql .= " AND 1";
        }
        
        $sql = "SELECT * FROM ".tname("threads")." WHERE {$wheresql} ORDER BY getdate DESC LIMIT {$offer},$pagesize";
        $count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT count(*) FROM ".tname("threads")." WHERE {$wheresql}"));
        $query = $_SGLOBAL['db']->query($sql);
        while($value = $_SGLOBAL['db']->fetch_array($query)){
            $value['dateline'] = $date->diff($value['getdate']);
            $value['subject'] = cubstr_same($value['subject']);
            $list[] = $value;
        }
        
        $pagecount = ceil($count/$pagesize);
        
        include template("apps/{$m}/{$apps_config[tpl]}{$a}");
        
		break;
	case "view":
        $time = date("m-d H:i",$_SGLOBAL['timestamp']);
        $page = isset($_GET['page'])?$_GET['page']:1; //PAGE参数用于翻页
        $pagesize = (int)20;
        if($page > 1){
            $offer = ($page - 1 ) * $pagesize;
        }else{
            $offer = 0;
        }
        $tid = $_GET['tid'];
        if(!$tid){
            exit('错误');
        }
        header("location:http://m.zhibotie.net/index_c_index_a_views_tid_{$tid}");
        exit();
        $query = $_SGLOBAL['db']->query("SELECT *  FROM ".tname("threads")." WHERE tid='{$tid}'");
        $subject = $_SGLOBAL['db']->fetch_array($query);
        $subject['dateline'] = $date->diff($subject['getdate']);
        $subject['message'] = bbcode($subject['message'],2);
        $subject['message'] = preg_replace_callback("/src=\"([^\"]+)/isu","replace_img",$subject['message']);
        if($subject){
            $sql = "SELECT * FROM ".tname("posts")." USE INDEX(hash) WHERE tid='{$tid}' AND hash='{$subject['hash']}' ORDER BY pid ASC limit {$offer},{$pagesize}";
            $count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT count(*) FROM ".tname("posts")." WHERE tid='{$tid}' AND hash='{$subject['hash']}'"));
            $query = $_SGLOBAL['db']->query($sql);
            $i = 0;
            while($value = $_SGLOBAL['db']->fetch_array($query)){
                $value['dateline'] = $date->diff($value['lastpost']);
                $value['message'] = bbcode($value['message'],2);
                $value['message'] = preg_replace_callback("/src=\"([^\"]+)/isu","replace_img",$value['message']);
                $value['num'] = ($page - 1) * $pagesize + $i + 1;
                $i++;
                $list[] = $value;
            }
            $pagecount = ceil($count/$pagesize);
        }else{
            exit("错误");
        }
        include template("apps/{$m}/{$apps_config[tpl]}list");
		break;
	//开始我的MARK菜单
    case "mymark":
        header("location:http://m.zhibotie.net/index_c_index_a_myremark");
        exit();
        $list = unserialize(stripslashes($_SCOOKIE['mymark']));
        if($list){
            $list_key = array_keys($list);
            foreach($list as $key => $value){
                $list_pid[] = $value['pid'];
                $list_dateline[] = $value['dateline'];
                $list_num[] = $value['num'];
            }
            $list_in = implode(",",$list_key);
            $sql = "SELECT * FROM ".tname("threads")." WHERE tid IN({$list_in})";
            $query = $_SGLOBAL['db']->query($sql);
            $i=0;
            while($value = $_SGLOBAL['db']->fetch_array($query)){
                $value['page'] = ceil($list_num[$i]/20);
                $value['pid'] = $list_pid[$i];
                $i++;
                $mylist[] = $value;
            }
        }
        include template("apps/{$m}/{$apps_config[tpl]}mymark");
        break;
}


function call_back_img($match){
    $md5 = current(explode(".",end(explode("/",$match[1]))));
    if($match){
        $string = "<div class=\"aligncenter size-full wp-image-657 div_imgs\" src=\"{$match[1]}\" alt=\"\" id=\"div_img_{$md5}\" onclick=\"loadImgs('{$md5}')\" href=\"javascript:;\">点击加载图片</div>";
    }
    return $string;
}


function replace_img($match){
    $match[0] = str_replace("&amp;","&",$match[0]);
    $string = "{$match[0]}&size=small";
    return $string;
}


unset($page);