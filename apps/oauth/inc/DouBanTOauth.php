<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include_once(S_ROOT."./apps/oauth/inc/OAuthCommon.php");
class DouBanTOauth{
    private $Wei_AKey = "011e93350c9f9f5a035bb99f25dede0e";
    private $Wei_Skey = "7334129cf5f13361";
    private $request_token_uri =  "http://www.douban.com/service/auth/request_token";
    private $authorize_uri =  "http://www.douban.com/service/auth/authorize";
    private $access_token_uri =  "http://www.douban.com/service/auth/access_token";
    private $oauth;
    public function __construct() {
        $this->oauth = new OauthCommon($this->Wei_AKey,  $this->Wei_Skey,  $this->request_token_uri,  $this->authorize_uri,  $this->access_token_uri);
    }
    
    public function GetUserAuthortionUri($call_back_uri){
        if(strstr($call_back_uri,"?")){
            $call_back_uri .= "&orignrequest="."douban";
        }else{
            $call_back_uri .= "?orignrequest="."douban";
        }
        $call_back_uri = urlencode($call_back_uri);
        $para = array();
        $responseData = $this->oauth->RequestToken($call_back_uri, $para);
        $http =new Http();
        $array = $http->GetQueryParameters($responseData);
        foreach ($array as $key=>$value){
            if($key == "oauth_token"){
                $_SESSION["douban_oauth_token"] = $value;
                $returnVal =  $this->oauth->AuthorizationURL($value);
                $returnVal.="&oauth_callback=" .$call_back_uri;
            }
            if($key == "oauth_token_secret"){
                $_SESSION["douban_oauth_token_secret"] = $value;
            }
        }
        return $returnVal;
    }
    
    public function GetAccessToken($verifier){
         $responseData = $this->oauth->GetAccessToken($verifier, $_SESSION["douban_oauth_token"] , $_SESSION["douban_oauth_token_secret"] );
         $http =new Http();
         $array = $http->GetQueryParameters($responseData);
         foreach ($array as $key=>$value){
            if($key == "oauth_token"){
                $_SESSION["douban_access_token"] = $value;
            }
            if($key == "oauth_token_secret"){
                $_SESSION["douban_access_token_secret"] = $value;
            }
        }
        return array("douban_access_token"=>$_SESSION["douban_access_token"],"douban_access_token_secret"=>$_SESSION["douban_access_token_secret"]);
        //return  $_SESSION["douban_access_token"]."^&^".$_SESSION["douban_access_token_secret"];
    }
    
    public function DouBanOAuthUser($oauth_token,$oauth_token_secret){
        $uri = "http://api.douban.com/people/@me?alt=json";
        $uri =  str_replace("@",  urlencode("@"),$uri);
        $para = array("alt"=>"json");
        $responseData = $this->oauth->SignRequest($uri, "get", $para, $oauth_token,$oauth_token_secret);
        return $responseData;
    }
    
    //��ȡ����
    public function DoubanEmail($oauth_token,$oauth_token_secret){
        $uri = "http://api.douban.com/doumail/inbox/unread";
        $para = array("alt"=>"json");
        $responseData = $this->oauth->SignRequest($uri, "get", $para, $oauth_token,$oauth_token_secret);
        return $responseData;
    }
    
    public function getDoubanEmail($noteid,$oauth_token,$oauth_token_secret){
        $uri = "http://api.douban.com/doumail/{$noteid}";
        $para = array("alt"=>"json");
        $responseData = $this->oauth->SignRequest($uri, "get", $para, $oauth_token,$oauth_token_secret);
        return $responseData;
    }
    
    public function DoubanAdd($context,$oauth_token,$oauth_token_secret){
        $uri = "http://api.douban.com/miniblog/saying";
        $requestBody = "<?xml version='1.0' encoding='UTF-8'?>";
        $requestBody .="<entry xmlns:ns0=\"http://www.w3.org/2005/Atom\" xmlns:db=\"http://www.douban.com/xmlns/\">";
        $requestBody .="<content>".$context."</content>";
        $requestBody .="</entry>";
        $responseData = $this->oauth->SignXMLRequest($uri, "post", $requestBody , $oauth_token,$oauth_token_secret);
        return $responseData;
    }
    
    //��ȡ�ҹ�ע����
    public function getcontacts($uid,$oauth_token,$oauth_token_secret){
        $uri = "http://api.douban.com/people/{$uid}/contacts";
        $para = array("alt"=>"json");
        $responseData = $this->oauth->SignRequest($uri, "get", $para, $oauth_token,$oauth_token_secret);
        return $responseData;
    }
    
    /*
     *@param 获取用户邻友动态
     */
    public function getUserminiblog($api,$oauth_token,$oauth_token_secret){
    	$uri = "http://api.douban.com/people/{$api}/miniblog/contacts";
    	$para = array("alt"=>"json");
        $responseData = $this->oauth->SignRequest($uri, "get", $para, $oauth_token,$oauth_token_secret);
        return $responseData;
    }
    
    
    public function sendDoubanEmail($api,$subject,$message,$oauth_token,$oauth_token_secret,$captcha = "" , $captcha_token = "" , $captcha_string = ""){
    	$uri =  "http://api.douban.com/doumails";
        $requestBody = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        $requestBody .= "<entry xmlns=\"http://www.w3.org/2005/Atom\" xmlns:db=\"http://www.douban.com/xmlns/\" xmlns:gd=\"http://schemas.google.com/g/2005\" xmlns:opensearch=\"http://a9.com/-/spec/opensearchrss/1.0/\">";
        $requestBody .= "<db:entity name=\"receiver\">";
        $requestBody .= "<uri>{$api}</uri>";
        $requestBody .= "</db:entity>";
        $requestBody .= "<content>{$message}</content>";
        $requestBody .= "<title>{$subject}</title>";
        if($captcha == "true"){
            $requestBody .= "<db:attribute name=\"captcha_token\">{$captcha_token}</db:attribute>";
            $requestBody .= "<db:attribute name=\"captcha_string\">{$captcha_string}</db:attribute>";
        }
        $requestBody .= "</entry>";
        $responseData = $this->oauth->SignXMLRequest($uri, "post", $requestBody , $oauth_token,$oauth_token_secret);
        return $responseData;
        
    }
    
    public function comments($mid , $oauth_token,$oauth_token_secret){
        $uri = "http://api.douban.com/miniblog/{$mid}/comments";
        $para = array("alt"=>"json");
        $responseData = $this->oauth->SignRequest($uri, "get", $para, $oauth_token,$oauth_token_secret);
        echo $responseData;
    }
    
    public function get_douban_user($api){
    	$uri = "http://api.douban.com/people/{$api}";
    	$para = array("alt"=>"json");
    	$responseData = $this->oauth->SignRequest($uri, "get", $para,"b2435afcbe91df2a16ca10f63bb59d7e", "f12ee8cb68dd3350");
    	return $responseData;
    }
}

?>
