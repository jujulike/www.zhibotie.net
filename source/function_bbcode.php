<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: function_bbcode.php 13104 2009-08-11 06:19:32Z xupeng $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

//格式化UBB代码，将UBB代码转换为 HTML代码
function bbcode($message, $parseurl=0,$ismoblie = FALSE) {
	global $_SGLOBAL;
	
	if(empty($_SGLOBAL['search_exp'])) {
		$_SGLOBAL['search_exp'] = array(
			"/\[face=(.+?)\](.+?)\[\/face\]/is",
			"/\[color=(.+?)\](.*?)\[\/color\]/is",
			"/\s*\[quote\][\n\r]*(.+?)[\n\r]*\[\/quote\]\s*/is",
			"/\[(url|url\s*tag=(.*?))\]\s*(https?:\/\/|ftp:\/\/|gopher:\/\/|news:\/\/|telnet:\/\/|rtsp:\/\/|mms:\/\/|callto:\/\/|ed2k:\/\/){1}([^\[\"']+?)\s*\[\/url\]/i",
			//"/\[url\]\s*(https?:\/\/|ftp:\/\/|gopher:\/\/|news:\/\/|telnet:\/\/|rtsp:\/\/|mms:\/\/|callto:\/\/|ed2k:\/\/){1}([^\[\"']+?)\s*\[\/url\]/i",
			"/\[em:([0-9]+):\]/i",
			"/\[flash\](.+?)\[\/flash\]/i",
		);
        if($ismoblie){
    		$_SGLOBAL['replace_exp'] = array(
    			"<font face=\"\\1\">\\2</font>",
    			"<font color=\"\\1\">\\2</font>",
    			"<div class=\"quote\"><span class=\"q\">\\1</span></div>",
    			"",
    			"<img src=\"image/face/\\1.gif\" class=\"face\">",
                "",
    		);
        }else{
            $_SGLOBAL['replace_exp'] = array(
            	"<font face=\\1>\\2</font>",
            	"<font color=\\1>\\2</font>",
    			"<div class=\"quote\"><span class=\"q\">\\1</span></div>",
                "<a href=\"\\3\\4\" target=\"_blank\">\\2</a>",
    			//"<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>",
    			"<img src=\"image/face/\\1.gif\" class=\"face\">",
            	"<embed class=\"video\" src=\"\\1\" quality=\"high\" width=\"480\" height=\"400\" align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\"></embed>",
    		);
        }
		$_SGLOBAL['search_str'] = array('[b]', '[/b]','[i]', '[/i]', '[u]', '[/u]','[strong]','[/strong]');
		$_SGLOBAL['replace_str'] = array('<b>', '</b>', '<i>','</i>', '<u>', '</u>','<strong>','</strong>');
	}
	
	if($parseurl==2) {//是否转换图片
		$_SGLOBAL['search_exp'][] = "/\[img\]\s*([^\[\<\r\n]+?)\s*\[\/img\]/ies";
		$_SGLOBAL['replace_exp'][] = 'bb_img(\'\\1\')';
		$message = parseurl($message);
	}
	@$message = str_replace($_SGLOBAL['search_str'], $_SGLOBAL['replace_str'],preg_replace($_SGLOBAL['search_exp'], $_SGLOBAL['replace_exp'], $message));
	
	//$message = nl2br(str_replace(array("\t", '   ', '  '), array('&nbsp; &nbsp; &nbsp; &nbsp; ', '&nbsp; &nbsp;', '&nbsp;&nbsp;'), $message));
	return str_replace(array("\r\n","\r","\n"), "", nl2br($message));
	
	
}

//转换连接 a 标记
function parseurl($message) {
	return preg_replace("/(?<=[^\]a-z0-9-=\"'\\/])((https?|ftp|gopher|news|telnet|mms|rtsp):\/\/)([a-z0-9\/\-_+=.~!%@?#%&;:$\\()|]+)/i", "[url]\\1\\3[/url]", ' '.$message);
}

//html转变为ubb代码
function html2bbcode($message) {
	global $_SGLOBAL;
	//是否开启UBB转换
	if(empty($_SGLOBAL['html_s_exp'])) {
		$_SGLOBAL['html_s_exp'] = array(
			"/<(embed|object)(.*)(data|src)=['|\"](.*?)['|\"](.*)><\/(embed|object)>/is",
			"/<font face=(.+?)>(.+?)\<\/font>/is",
			"/<font color=(.+?)>(.+?)\<\/font>/is",
			"/\<div class=\"quote\"\>\<span class=\"q\"\>(.*?)\<\/span\>\<\/div\>/is",
			"/\<a href=\"(.+?)\"(.*?)\>(.*?)\<\/a\>/is",
			"/(\r\n|\n|\r)/",
			"/<br.*>/siU",
			"/[ \t]*\<img(.*?)src=\"image\/face\/(.+?).gif\".*?\>[ \t]*/is",
			"/\s*\<img(.*?)src=\"(.+?)\".*?\>\s*/is",
            "/<p(.*?)>/is",
            "/<\/p>/is",
			"/<span(.*?)>/is",
			"/<\/span>/is",
			
		);
		$_SGLOBAL['html_r_exp'] = array(
			"[flash]\\4[/flash]",
			"[face=\\1]\\2[/face]",
			"[color=\\1]\\2[/color]",
			"[quote]\\1[/quote]",
			"\n[url tag=\\3]\\1[/url]\n",
			"",
			"\n",
			"[em:\\1:]",
			"\n[img]\\2[/img]\n",
            "\n",
            "\n",
			"",
			
		);
		$_SGLOBAL['html_s_str'] = array("<STRONG>","</STRONG>","<B>","</B>","<strong>","</strong>",'<b>', '</b>', '<i>','</i>', '<u>', '</u>', '&nbsp; &nbsp; &nbsp; &nbsp; ', '&nbsp; &nbsp;', '&nbsp;&nbsp;', '&lt;', '&gt;');
		$_SGLOBAL['html_r_str'] = array('[strong]','[/strong]','[b]','[/b]','[strong]','[/strong]','[b]', '[/b]','[i]', '[/i]', '[u]', '[/u]', "\t", '   ', '  ', '<', '>');
		
	}	
	
	@$message = str_replace($_SGLOBAL['html_s_str'], $_SGLOBAL['html_r_str'],
		preg_replace($_SGLOBAL['html_s_exp'], $_SGLOBAL['html_r_exp'], $message));
		
	$message = shtmlspecialchars($message);
	
	return trim($message);
}

function bb_img($url) {
	//$url = addslashes($url);
	return "<img src=\"".str_replace("&amp;","&",$url)."\">";
}

?>