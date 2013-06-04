<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include_once(S_ROOT."./apps/oauth/inc/OAuthCommon.php");
class RenRenOauth{
    private $Wei_AKey = "4502d1b9184f49dcbb66cd1d6b8f00d7";
    private $Wei_Skey = "7182846bd7b3427ab3b620c39547cd05";
    private $authorize_uri = "https://graph.renren.com/oauth/authorize";
    private $access_token_uri =  "https://graph.renren.com/oauth/token";
    private $session_key_uri = "https://graph.renren.com/renren_api/session_key";
    private $callback_uri = "http://www.zhibotie.net/apps.php?m=oauth&a=index&op=callback&orignrequest=renren";
    private $api_uri = "http://api.renren.com/restserver.do";
    private $scope = "status_update";
    private $oauth;
    public function __construct() {
        $this->oauth = new OauthCommon($this->Wei_AKey,  $this->Wei_Skey,  $this->authorize_uri,  $this->access_token_uri,  $this->session_key_uri);
    }
    
    public function GetRenRenCode(){
        return $this->oauth->GetAuthorizationCode(urlencode($this->callback_uri), $this->scope);
    }
     
    public function GetAccessToken($code){
        $reponseData = $this->oauth->Get2AccessToken($code, urlencode($this->callback_uri));
        $obj = json_decode($reponseData); 
        return (array)$obj;
    }
    
    //»»ÊÚÈ¨
    public function GetSessionKey($access_token){
        $reponseData = $this->oauth->GetSessionKey($access_token);
        $obj = json_decode($reponseData); 
        $_SESSION["renren_access_token"] = $obj->renren_token->session_key;
        $_SESSION["renren_access_token_secret"] = $obj->renren_token->session_secret;
        return array("renren_access_token"=>$_SESSION['renren_access_token'],"renren_access_token_secret"=>$_SESSION['renren_access_token_secret']);
    }
 
    public function SessionKey(){
        return $_SESSION["renren_session_key"];
    }
    
    public function GetLoggedInUser($SessionKey,$call_mode=false){
        $call_mode = $call_mode==false?"users.getLoggedInUser":$call_mode;
        $paras = array("method"=>$call_mode);
        return $this->oauth->CallRequest($this->api_uri, $paras,$SessionKey , "json","renren", "");
    }
    
    public function GetUsersInfo($SessionKey,$call_mode=false){
        $call_mode = $call_mode==false?"users.getInfo":$call_mode;
        $paras = array("method"=>$call_mode,"fields"=>"uid,name,sex,star,zidou,vip,birthday,email_hash,tinyurl,headurl,mainurl,hometown_location,work_history,university_history");
        return $this->oauth->CallRequest($this->api_uri, $paras,$SessionKey , "json","renren", "");
    }
    
    public function StatusUpdate($context,$SessionKey,$call_mode=false){
        $call_mode = $call_mode==false?"status.set":$call_mode;
        $http = new Http();
        $context = $http->utf82Unicode($context);
        $paras = array("method"=>$call_mode,"status" => $context);
        return $this->oauth->CallRequest($this->api_uri, $paras,$SessionKey , "json","renren", "");
    }
}
?>