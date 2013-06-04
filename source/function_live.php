<?php
/**
 * @param 内裤叔叔 分离 live.php 文件
 * @param live 函数
 */


if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

/**
 * @param 获取豆瓣地址
 * @param string $url
 * @param bol $issubject
 * @return array();
 */
function _getDouban($url,$issubject = true){
	$douban = array();
	$content = fopen_url($url);
	$content = mb_convert_encoding($content['content'],"utf-8","utf-8");//iconv("utf-8","utf-8",$content['content']);
	//匹配完整的标题
	if(preg_match("#<table\s*class=\"infobox\"\s*width=\"100%\">(.*?)</table>#s",$content,$subject)){
		$douban['subject'] = trim(strip_tags(preg_replace("#<strong>(.*?)</strong>#","",$subject[1])));
	}else{
		preg_match("#<title>(.*?)</title>#s",$content,$title);
		$douban['subject'] = trim($title[1]);
	}
	//匹配用户信息
	preg_match("#<h3>(.*?)</h3>#is",$content,$aouthinfo);
	//时间
	preg_match("/<span class=\"color-green\">(.*?)<\/span>/is", $aouthinfo[1],$d_time);
	$douban['dateline'] = (int)strtotime(trim($d_time[1]));
	//签名
	preg_match("#\((.*?)\)#",$aouthinfo[1],$author_sutus);
	$douban['status'] = $author_sutus[1];
	//作者
	preg_match("#<a\s*href=\"(.*?)\">(.*?)<\/a>#is",$aouthinfo[1],$author);
	$douban['author'] = (string)trim($author[2]);
	$douban['hash'] = md5(trim($author[1]));
	if(substr(trim($author[1]),-1,strlen(trim($author[1]))) == "/"){
		$hashurl[1] = substr(trim($author[1]),0,strlen(trim($author[1]))-1);
	}
	$douban['api'] = end(explode("/",$hashurl[1]));
	preg_match("#<img\s*class=\"pil\"\s*src=\"(.*?)\" #s",$content,$subject_head);
	$douban['head'] = trim($subject_head[1]);

	preg_match("/<div\s*class=\"topic-content\">(.*?)<\/p>\s*<\/div>/is", $content, $matches);
	$string = trim($matches[1]."</p>");
	$string =  str_replace(array("</div>","<div class=\"topic-figure cc\">","<div class=\"clear\">","<div class=\"topic-figure ll\">","</span>","<span class=\"topic-figure-title\">")," ",$string);
	//ת�����ӵ�ַ
	$string = preg_replace_callback("/\<a href=\"(.+?)\"\>(.*?)\<\/a\>/is","call_back_url",$string);
	$string = preg_replace_callback("/src=\"([^\"]+)/isu","get_image_replace",$string);
	$douban['message'] = trim($string);
	//��ȡtag
	preg_match("#<div class=\"title\">(.*?)<\/div>#is",$content,$tag);
	$temp_tag = explode("/",$tag[1]);
	$douban['tags'] = $temp_tag[4];//trim(str_replace(array("&gt;",cplang("douban_back"),cplang("douban_group")),"",strip_tags($tag[1])));
	if($issubject){
		preg_match_all("#<li class=\"clearfix(.*?)>(.*?)</li>#s",$content,$reply);
		foreach($reply[2] as $key => $value){
			preg_match("#<p>(.*?)</p>#",$value,$message);  //��������
			if(preg_match("#<span\s*class=\"all\">(.*?)</span>#is",$value,$qu)){
				$quote = "<div class=\"quote\"><span class=\"q\">".$qu[1]."</span></div>";
			}else{
				$quote = "";
			}
			$douban['reply'][$key]['message'] = trim(str_replace(array("<p>","</p>"),"",$quote.$message[0]));
			//匹配用户信息
			preg_match("#<h4>(.*?)</h4>#is",$value,$info);
			preg_match("/<span class=\"pubtime\">(.*?)<\/span>/is", $info[1],$r_time);
			//签名
			preg_match("#\((.*?)\)#",$info[1],$r_sutus);
			preg_match("#<a\s*href=\"(.*?)\">(.*?)<\/a>#is",$info[1],$r_author);
			preg_match("#src=\"(.*?)\" #",$value,$headerimg);
			$douban['reply'][$key]['hash'] = md5(trim($r_author[1]));
			$douban['reply'][$key]['lastpost'] = (int)strtotime(trim($r_time[1]));  //ʱ��
			$douban['reply'][$key]['author'] = (string)trim($r_author[2]);
			$douban['reply'][$key]['status'] = trim(strip_tags($r_sutus[1]));
			$douban['reply'][$key]['head'] = $headerimg[1];
		}
	}
	preg_match_all('#\?start=(\d+?)" #is',$content,$max);
	$douban['maxpage'] = intval(max($max[1]) / 100 + 1);  //最大列表
	if(strpos($url,'?start=')){
		$string = split('\?start=',$url);
		$douban['thispage'] = intval($string[1]/100 + 1);  //当前页
		$douban['fromurl'] = trim($string[0]); //ԭ��
	}else{
		$douban['thispage'] = 0;
		$douban['fromurl'] = $url;
	}
	$douban['urlhash'] = md5($douban['fromurl']); //ԭ��MD5

	return $douban;
}

function _tianya($url,$iscommont = true,$issubject = true){
	$tianya = array();
	$content = fopen_url($url);

	//强制转换编码
	$content = mb_convert_encoding($content['content'],"utf-8","utf-8");
	//匹配各种信息
	//标题
	preg_match_all("/(js_activityuserid|js_blockid|js_postid|js_posttime|js_title|url|js_blockname|js_activityusername)=\"(.*?)\"/i",$content,$subject);
	$tianya['dateline'] = substr(trim($subject[2][3]), 0,10);
	$tianya['subject'] = urldecode(trim($subject[2][4]));
	$tianya['fromurl'] = trim($subject[2][5]);
	$tianya['urlhash'] = md5($tianya['fromurl']);
	$tianya['tags'] = urldecode(trim($subject[2][6]));
	$tianya['author'] = urldecode(trim($subject[2][7]));
	$tianya['hash'] = md5($tianya['author']);
	$tianya['api'] = trim($subject[2][0]);
	$js_blockid = trim($subject[2][1]);
	$js_postid = trim($subject[2][2]);
	//获取最大一页
	preg_match("/onsubmit=\"return goPage\(this\,\'{$js_blockid}\'\,{$js_postid}\,(\d+)\);\"/is", $content,$maxpage);
	$tianya['maxpage'] = $maxpage[1]?$maxpage[1]:1;
	//获取当前页
	$tianya['thispage'] = current(explode(".", end(explode("-", $tianya['fromurl']))));
	$tianya['nextpage'] = $tianya['thispage'] + 1;
	$tianya['head'] = "http://tx.tianyaui.com/logo/{$tianya['api']}";
	//主帖内容
	preg_match("/<div class=\"bbs-content clearfix\">(.*?)<\/div>/is",$content,$message);
	$tianya['message'] = $message[1];
	$tianya['message'] = str_replace(array("<div class=\"post-jb\">",lang("tianya_3g"),lang("tianya_app"),"来自UC浏览器","http://m.tianya.cn/web/","手机上天涯，随时围观热点：m.tianya.cn"),"",$tianya['message']);
	$tianya['message'] = preg_replace_callback("#<img(.*?)>#","get_image_tianya",$tianya['message']);
	if($issubject){
		//回帖信息
		preg_match_all("/<div class=\"atl-head-reply\">(.*?)<\/div>/is",$content,$reinfo);
		preg_match_all("/<div class=\"bbs-content\">(.*?)<\/div>/is", $content, $remessage);
		if($iscommont) array_shift($reinfo[1]);  
		foreach ($reinfo[1] as $key => $value){
			preg_match_all("/(replytime|author|authorId)=\"(.*?)\"/is", $value, $author);
			$tianya["reply"]["author"][] = trim($author[2][1]);
			$tianya["reply"]["hash"][] = md5($tianya["reply"]["author"][$key]);
			$tianya["reply"]["lastpost"][] = (int)strtotime(trim($author[2][0]));
			$tianya['reply']['head'][] = "http://tx.tianyaui.com/logo/{$author[2][2]}";
		}
		foreach ($remessage[1] as $key => $value){
			$value = str_replace(array("<div class=\"post-jb\">",lang("tianya_3g"),lang("tianya_app"),"来自UC浏览器","http://m.tianya.cn/web/","手机上天涯，随时围观热点：m.tianya.cn"),"",$value);
			$value = preg_replace_callback("#<img(.*?)>#","get_image_tianya",$value);
			//$tianya["reply"]["message"][] = $remessage[1][$key];
			$tianya["reply"]["message"][] = $value;
		}
	}
	return $tianya;
}


/**
 * @param 获取百度连接地址
 */
function _baidu($url){
	$header = array("Referer: http://www.baidu.com/");
	$content = fopen_url($url,1,$header);
	$content = mb_convert_encoding($content['content'],"utf-8","GBK");

	preg_match("/<h1(.*?)>(.*?)<\/h1>/is",$content,$subject);

	//print_r($subject);
	preg_match_all("#<cc>(.*?)</cc>#is",$content,$message_array);
	preg_match("#forum_name:\"(.*?)\"#is",$content,$tags);
	preg_match_all("#<div class=\"l_post( | noborder)\" data-field=('|\")(.*?)\\2#is",$content,$info);

	preg_match("#PageData.pager\s*=\s*{(.*?)}#",$content,$page);
	$baidu = array();
	$baidu['subject'] = $subject[2];
	foreach($message_array[1] as $key => $value){
		$value = html2bbcode($value);
		$value = preg_replace_callback("/\[img\](.*?)\[\/img\]/s","get_image_baidu",$value);
		$info[3][$key] = str_replace("&quot;","\"",$info[3][$key]);

		$json = json_decode($info[3][$key],TRUE);

		$baidu['author'][] = $json["author"]["name"];
		$baidu['api'][] = $json["author"]["outer_id"];
		$baidu['hash'][] = md5($json["author"]["outer_id"]);
		$baidu['message'][] = preg_replace("/\&lt\;(.*?)\&gt\;/", "",$value);
		$baidu['dateline'][] = strtotime($json['content']['date']);
		$baidu['head'][] = "http://tb.himg.baidu.com/sys/portrait/item/".$json['author']['portrait'];
	}

	$baidu['urlhash'] = md5($url);
	$baidu['fromurl'] = $url;
	$p = json_decode("{{$page[1]}}",TRUE);
	$baidu['maxpage'] = $p['total_page'];
	$baidu['thispage'] = $p['cur_page'];
	$baidu['tags'] = $tags[1];
	return $baidu;
}



/**
 * @param 获取豆瓣图片
 */
function get_image_replace($matches,$type="douban"){
	$string = "thumbp.php?uri=" .urlencode($matches[1]) . "&from=&path=douban&remote=1";
	return "src=\"" .$string;
}

/**
 * @param 获取天涯图片
 */
function get_image_tianya($matches){
	preg_match("#original=\"(.*?)\"#",$matches[1],$newurl);
	if(!$newurl){
		preg_match("#src=\"(.*?)\"#",$matches[1],$newurl);
	}
	if($newurl[1]){
		return "<img src=\"thumbp.php?uri=" .urlencode($newurl[1]) . "&from=". urlencode("http://*.laibafile.cn") ."&path=tianya&remote=1\"  onload=\"javascript:ResetImageSize(this,900);\" />";
	}else{
		return "";
	}
}

/**
 * @param 获取百度图片
 */
function get_image_baidu($match){
	if(strexists($match[1],"http://imgsrc.baidu.com/forum/")){
		$string = "thumbp.php?uri=" .urlencode($match[1]) . "&from=baidu.com&path=baidu&remote=1";
		return "[img]{$string}[/img]";
	}else{
	return "[img]{$match[1]}[/img]";
	}
	}

function check_url_is_ok($url = ""){
	$array = @get_headers($url,1);
	if(preg_match('/200/',$array[0]) || preg_match('/301/',$array[0])) {
	return TRUE;
	}else{
	return FALSE;
	}
}


	/**
	* @param 回调地址
	*/
function call_back_url($match){
	if($match[2]){
	$match[2] = str_replace(array("<wbr/>"),"",$match[2]);
	$string = "<a href=\"{$match[2]}\">{$match[2]}</a>";
	}
	return $string;
}

	/**
		* @param 随机姓名
		*/
function rand_name(){
	$userlist = cplang("rand_array");
	return $userlist[array_rand($userlist)];
	}
