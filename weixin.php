<?php
/**
  * wechat php test
  */


include_once('./common.php');

define("TOKEN", "zhibotietoken");
$wechatObj = new wechatCallbackapiTest();
//获取POST数据
$postStr = file_get_contents("php://input","r");
$wechatObj->responseMsg($postStr);

class wechatCallbackapiTest
{
	private $allow_key = array("直播贴","狗血","");
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg($postStr)
    {
    	global $_SGLOBAL;
		//格式化
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $msgType = "text";
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";             
				if(!empty( $keyword ))
                {
                	$sql = "SELECT count(*) as sum FROM ".tname("threads")." WHERE subject LIKE ('%{$keyword}%') OR tags LIKE ('%{$keyword}%')";
                	$query = $_SGLOBAL['db']->query($sql);
                	$data = $_SGLOBAL['db']->fetch_array($query);
                	if($data && $data['sum'] > 0){
                		$rand = mt_rand(0, $data['sum'] - 1);
	                	$sql = "SELECT tid,subject,message,tags,taglist,hotimg,displayorder,dateline FROM ".tname("threads")." WHERE subject LIKE ('%{$keyword}%') OR tags LIKE ('%{$keyword}%') ORDER BY tid Desc LIMIT {$rand},1 ";
	                	
	                	$query = $_SGLOBAL['db']->query($sql);
	                	$result = $_SGLOBAL['db']->fetch_array($query);
	                	$textTpl = "
	                		<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[%s]]></MsgType>
								<Content></Content>
		                		<ArticleCount>1</ArticleCount>
							     <Articles>
								     <item>
								     <Title><![CDATA[%s]]></Title>
								     <Discription><![CDATA[%s]]></Discription>
								     <PicUrl><![CDATA[%s]]></PicUrl>
								     <Url><![CDATA[%s]]></Url>
								     </item>
							     </Articles>
							     <FuncFlag>0</FuncFlag>
	                		 </xml>";  
	                	$picurl = strstr($result['hotimg'],".thumb.jpg")?$result['hotimg']:str_replace("!big", "!jian.140", $result['hotimg']);
	                	$uri = "http://m.zhibotie.net/index_c_index_a_views_tid_{$result['tid']}.html";
	                	$title = $result['subject'];
	                	$result['message'] = replace_ubb_html($result['message']);
	                	$content = getstr($result['message'], 250);
	                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $result['dateline'], "news",  $title , $content , $picurl , $uri);
	                	runlog('send_weixin', "tid: {$result['tid']} send succeed. key：{$keyword} , fromname : {$fromUsername} , type: news ");
	                	echo $resultStr;
                	}else{
                		$msgType = "text";
                		$content = "糟糕，系统好像没有找到“{$keyword}”相关信息！\r\n 直接输入“直播贴”看看 :p ";
                		$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $content);
                		runlog('send_weixin', "data error , errorcode :系统好像没有找到 . key：{$keyword} , fromname : {$fromUsername}");
                		echo $resultStr;
                	}
                }else{
                	$msgType = "text";
                	$content = "亲，你是不是大姨妈来了？怎么好像什么都问我！\r\n 直接输入“直播贴”看看 :p ";
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $content);
                	runlog('send_weixin', "data error , errorcode :need key , fromname : {$fromUsername}");
                	echo $resultStr;
                }

        }else {
        	exit();
        }
    }
		
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
	
	
	private function get_rows($key){
		
	}
}

?>