<?php
include_once(S_ROOT."./apps/oauth/inc/sina.v2.class.php");

/**
 * 
 * 新浪授权类
 * @author 内裤叔叔
 * @version 0.1;
 * @param 用于sina oauth 2.0;
 *
 */
class sinaClass{
	
	private  $key = "453438083"; //私有化KEY
	private $skye = "5462040d8340dad62e6285beb347f5d7"; //私有化SKEY；
	private $callback = "http://www.zhibotie.net/apps.php?m=oauth&a=sina&option=callback"; //私有化回调地址；
	
	/**
	 * 
	 * 初始化V2 类
	 */
	private function init(){
		$newSina = new SaeTOAuthV2($this->key,$this->skye);
		return $newSina;
	}
	
	/**
	 * 获取回调
	 */
	public function get_callback(){
		return $this->callback;
	}
	/**
	 * 
	 * 初始化授权地址
	 */
	public function oauth_url(){
		$o = $this->init();
		return $o->getAuthorizeURL($this->callback);
	}
	
	/**
	 * 认证回调地址
	 */
	public function access_token($code = array()){
		$o = $this->init();
		return $o->getAccessToken("code",$code);
	}
	
	/**
	 * 发微博
	 */
	public function update($skey,$content){
		$sinaweibo = new SaeTClientV2($this->key, $this->skye, $skey);
		return $sinaweibo->update($content);
	}
	
	public function update_img($skey,$content,$path){
		$sinaweibo = new SaeTClientV2($this->key, $this->skye, $skey);
		return $sinaweibo->upload($content, $path);
	}
	
	public function get_user_info($skey,$uid){
		$sinaweibo = new SaeTClientV2($this->key, $this->skye, $skey);
		return $sinaweibo->show_user_by_id($uid); 
	}
}