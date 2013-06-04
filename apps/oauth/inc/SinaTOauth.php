<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include_once(S_ROOT."./apps/oauth/inc/OAuthCommon.php");
if (!session_id()) session_start();
class SinaTOauth{
    private $Wei_AKey = "925507530";
    private $Wei_Skey = "39dc6584dee2213ee87379507c83c0f1";
    private $request_token_uri = "http://api.t.sina.com.cn/oauth/request_token";
    private $authorize_uri = "http://api.t.sina.com.cn/oauth/authorize";
    private $access_token_uri =  "http://api.t.sina.com.cn/oauth/access_token";
    private $oauth;
    public function __construct() {
        $this->oauth = new OauthCommon($this->Wei_AKey,  $this->Wei_Skey,  $this->request_token_uri,  $this->authorize_uri,  $this->access_token_uri);
    }
    
    public function GetUserAuthortionUri($call_back_uri){
        if(strstr($call_back_uri,"?")){
            $call_back_uri .= "&orignrequest="."sina";
        }else{
            $call_back_uri .= "?orignrequest="."sina";
        }
        $call_back_uri = urlencode($call_back_uri);
        $para = array();
        $responseData = $this->oauth->RequestToken($call_back_uri, $para);
        $http =new Http();
        $array = $http->GetQueryParameters($responseData);
        foreach ($array as $key=>$value){
            if($key == "oauth_token"){
                $_SESSION["sina_oauth_token"] = $value;
                $returnVal =  $this->oauth->AuthorizationURL($value);
                $returnVal.="&oauth_callback=" .$call_back_uri;
            }
            if($key == "oauth_token_secret"){
                $_SESSION["sina_oauth_token_secret"] = $value;
            }
        }
        return $returnVal;
    }
    
    public function GetAccessToken($verifier){
        
         $responseData = $this->oauth->GetAccessToken($verifier, $_SESSION["sina_oauth_token"] , $_SESSION["sina_oauth_token_secret"]);

         $http =new Http();
         $array = $http->GetQueryParameters($responseData);
         foreach ($array as $key=>$value){
            if($key == "oauth_token"){
                $_SESSION["sina_access_token"] = $value;
            }
            if($key == "oauth_token_secret"){
                $_SESSION["sina_access_token_secret"] = $value;
            }
        }
        return array("sina_access_token"=>$_SESSION["sina_access_token"],"sina_access_token_secret"=>$_SESSION["sina_access_token_secret"]);
    }
    
    public function VerifyCredentials($oauth_token,$oauth_token_secret){
        $uri = "http://api.t.sina.com.cn/account/verify_credentials.json";
        $para = array("format"=>"json");
        $responseData = $this->oauth->SignRequest($uri, "get", $para, $oauth_token,$oauth_token_secret);
        return $responseData;
    }
    
    public function SinaAdd($message,$oauth_token, $oauth_token_secret){
         $uri = "http://api.t.sina.com.cn/statuses/update.json";
         $para = array("format"=>"json","status"=>$message);
         $responseData = $this->oauth->SignRequest($uri, "post", $para, $oauth_token, $oauth_token_secret);
         print_r( $responseData);
    }
	
    public function SinaAddPic($message , $pic ,$oauth_token, $oauth_token_secret){
        $uri = "http://api.t.sina.com.cn/statuses/upload.json";
        $para = array("format"=>"json","status"=>$message,"pic"=>$pic);
        $responseData = $this->oauth->SignRequest($uri, "post", $para, $oauth_token, $oauth_token_secret);
        return $responseData;        
    }
    
	//获取新浪短网址
    public function Sinaurl($url) {
        $opts['http'] = array('method' => "GET", 'timeout'=>60,);
        $context = stream_context_create($opts);
        $url = "https://api.weibo.com/2/short_url/shorten.json?source=".$this->Wei_AKey."&url_long=".urlencode($url);
        $html =  @file_get_contents($url,false,$context);
        $url = json_decode($html,true);
        if ($url['urls'][0]['result']) {
            return $url['urls'][0]['url_short'];
        }
    }
}

?>
