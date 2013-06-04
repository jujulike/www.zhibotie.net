<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include_once("inc/OAuthCommon.php");
class TencentTOauth{
    private $Wei_AKey = "ÄãµÄÌÚÑ¶Î¢²©AppKey";
    private $Wei_Skey = "ÄãµÄÌÚÑ¶Î¢²©AppKeySercet";
    private $request_token_uri = "http://open.t.qq.com/cgi-bin/request_token";
    private $authorize_uri = "http://open.t.qq.com/cgi-bin/authorize";
    private $access_token_uri = "http://open.t.qq.com/cgi-bin/access_token";  
    private $oauth;
    public function __construct() {
        $this->oauth = new OauthCommon($this->Wei_AKey,  $this->Wei_Skey,  $this->request_token_uri,  $this->authorize_uri,  $this->access_token_uri);
    }
    
    public function GetUserAuthortionUri($call_back_uri){
        $call_back_uri .= "?orignrequest="."qq";
        $para = array();
        $responseData = $this->oauth->RequestToken($call_back_uri, $para);
        $http =new Http();
        $array = $http->GetQueryParameters($responseData);
        $returnVal;
        foreach ($array as $key=>$value){
            if($key == "oauth_token"){
                $_SESSION["qq_oauth_token"] = $value;
                $returnVal =  $this->oauth->AuthorizationURL($value);
            }
            if($key == "oauth_token_secret"){
                $_SESSION["qq_oauth_token_secret"] = $value;
            }
        }
        return $returnVal;
    }
    
    public function GetAccessToken($verifier){
         $responseData = $this->oauth->GetAccessToken($verifier, $_SESSION["qq_oauth_token"] , $_SESSION["qq_oauth_token_secret"]);
         $http =new Http();
         $array = $http->GetQueryParameters($responseData);
         foreach ($array as $key=>$value){
            if($key == "oauth_token"){
                $_SESSION["qq_access_token"] = $value;
            }
            if($key == "oauth_token_secret"){
                $_SESSION["qq_access_token_secret"] = $value;
            }
        }
        return  $_SESSION["qq_access_token"]."^&^".$_SESSION["qq_access_token_secret"];
    }
    
    public function QQAuthUser(){
         $uri = "http://open.t.qq.com/api/user/info";
         $para = array("format"=>"json");
         $responseData = $this->oauth->SignRequest($uri, "get", $para, $_SESSION["qq_access_token"], $_SESSION["qq_access_token_secret"]);
         return $responseData;
    }
    
    public function QWeiAdd($context){
         $uri = "http://open.t.qq.com/api/t/add";
         $para = array("format"=>"json","content"=>$context,"clientip"=>"123.119.32.211");
         $responseData = $this->oauth->SignRequest($uri, "post", $para, $_SESSION["qq_access_token"], $_SESSION["qq_access_token_secret"]);
         return $responseData;
    }
}

?>
