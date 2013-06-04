<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include_once(S_ROOT."./apps/oauth/inc/OAuthCommon.php");
class NtesTOauth{
    private $Wei_AKey = "ÄãµÄÍøÒ×Î¢²©AppKey";
    private $Wei_Skey = "ÄãµÄÍøÒ×Î¢²©AppKeySercert";
    private $request_token_uri =  "http://api.t.163.com/oauth/request_token";
    private $authorize_uri = "http://api.t.163.com/oauth/authenticate";
    private $access_token_uri =  "http://api.t.163.com/oauth/access_token";
    private $oauth;
    public function __construct() {
        $this->oauth = new OauthCommon($this->Wei_AKey,  $this->Wei_Skey,  $this->request_token_uri,  $this->authorize_uri,  $this->access_token_uri);
    }
    
    public function GetUserAuthortionUri($call_back_uri){
        $call_back_uri .= "?orignrequest="."ntes";
        $para = array();
        $responseData = $this->oauth->RequestToken($call_back_uri, $para);
        $http =new Http();
        $array = $http->GetQueryParameters($responseData);
        $returnVal;
        foreach ($array as $key=>$value){
            if($key == "oauth_token"){
                $_SESSION["ntes_oauth_token"] = $value;
                $returnVal =  $this->oauth->AuthorizationURL($value);
                $returnVal.="&oauth_callback=" .$call_back_uri;
            }
            if($key == "oauth_token_secret"){
                $_SESSION["ntes_oauth_token_secret"] = $value;
            }
        }
        return $returnVal;
    }
    
    public function GetAccessToken($verifier){
         $responseData = $this->oauth->GetAccessToken($verifier, $_SESSION["ntes_oauth_token"] , $_SESSION["ntes_oauth_token_secret"]);
         $http =new Http();
         $array = $http->GetQueryParameters($responseData);
         foreach ($array as $key=>$value){
            if($key == "oauth_token"){
                $_SESSION["ntes_access_token"] = $value;
            }
            if($key == "oauth_token_secret"){
                $_SESSION["ntes_access_token_secret"] = $value;
            }
        }
        return  $_SESSION["ntes_access_token"]."^&^".$_SESSION["ntes_access_token_secret"];
    }
    
    public function NtesOAuthUser(){
        $uri = "http://api.t.163.com/account/verify_credentials.json";
        $para = array("format"=>"json");
        $responseData = $this->oauth->SignRequest($uri, "get", $para, $_SESSION["ntes_access_token"], $_SESSION["ntes_access_token_secret"]);
        return $responseData;
    }
    
    public function NtesWeiAdd($context){
         $uri =   "http://api.t.163.com/statuses/update.json";
         $para = array("format"=>"json","status"=>$context);
         $responseData = $this->oauth->SignRequest($uri, "post", $para, $_SESSION["ntes_access_token"], $_SESSION["ntes_access_token_secret"]);
         return $responseData;
    }
    
}

?>
