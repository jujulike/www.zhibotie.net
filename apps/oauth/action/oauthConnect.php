<?php

/**
 *@author �ڿ�����
 *@param �û�����΢�������ꡢ���ˡ�QQ����վ��Ȩ���⣻
 *@className oauthConnect;
 */
class oauthConnect
{

    /**
     *@param function Name include_xxxxOauth;
     * @return object;
     */
    public function include_DouBanTOauth()
    {
        include_once (S_ROOT."./apps/oauth/inc/DouBanTOauth.php");
    }
    public function include_SinaTOauth()
    {
        include_once (S_ROOT."./apps/oauth/inc/SinaTOauth.php");
    }
    public function include_TencentTOauth()
    {
        include_once (S_ROOT."./apps/oauth/inc/TencentTOauth.php");
    }
    public function include_SohuTOauth()
    {
        include_once (S_ROOT."./apps/oauth/inc/SohuTOauth.php");
    }
    public function include_RenRenOauth()
    {
        include_once (S_ROOT."./apps/oauth/inc/RenRenOauth.php");
    }
    public function include_NtesTOauth()
    {
        include_once (S_ROOT."./apps/oauth/inc/NtesTOauth.php");
    }

    /**
     * @param ��¼��Ȩ��α���ַ
     * @param string $type ��Ȩ����: douban=>����, sina=>���� , qq=>��ѶQQ��
     * @param string $callback_uri ��Ȩ�ص���ַ; renren�ص���ַ��Ҫ�ֶ�ָ����
     * @return string url;
     */
    public function rquest_token($type, $callback_uri)
    {
        switch (strtolower($type))
        {
            case "qq":
                $this->include_TencentTOauth();
                $qq = new TencentTOauth();
                $return = $qq->GetUserAuthortionUri($callback_uri);
                break;
            case "renren":
                $this->include_RenRenOauth();
                $renren = new RenRenOauth();
                $return = $renren->GetRenRenCode();
                break;
            case "douban":
                $this->include_DouBanTOauth();
                $douban_list = new DouBanTOauth();
                $return = $douban_list->GetUserAuthortionUri($callback_uri);
                break;
            case "sina":
                $this->include_SinaTOauth();
                $sina = new SinaTOauth();
                $return = $sina->GetUserAuthortionUri($callback_uri);
                break;
            case "souhu":
                $this->include_SohuTOauth();
                $souhu = new SohuTOauth();
                $return = $souhu->GetUserAuthortionUri($callback_uri);
                break;
            case "ntes":
                $this->include_NtesTOauth();
                $ntes = new NtesTOauth();
                $return = $ntes->GetUserAuthortionUri($callback_uri);
                break;
        }
        return $return;
    }

    /**
     * @param ����Ȩ��ַ����ȡ��Ȩ��;
     * @param string $type;
     */
    public function get_access_token($type, $oauth_verifier, $oauth_token = "")
    {

        switch ($type)
        {
            case "douban":
                $this->include_DouBanTOauth();
                $douban = new DouBanTOauth();
                $array = $douban->GetAccessToken($oauth_token);
                break;
            case "sina":
                $this->include_SinaTOauth();
                $sina = new SinaTOauth();
                $array = $sina->GetAccessToken($oauth_verifier);
                break;
            case "renren":
                $this->include_RenRenOauth();
                $renren = new RenRenOauth();
                $arr = $renren->GetAccessToken($oauth_verifier);
                if(is_array($arr)){
                    $array = $renren->GetSessionKey($arr["access_token"]);
                }else{
                    $array = false;
                }
        }
        return $array;

    }


    public function get_user_info($type, $oauth_token, $oauth_token_secret)
    {
        switch ($type)
        {
            case "douban":
                $this->include_DouBanTOauth();
                $douban = new DouBanTOauth();
                $json = $douban->DouBanOAuthUser($oauth_token, $oauth_token_secret);
                $json = json_decode($json, true);
                $user['token_id'] = $json['db:uid']["\$t"];
                $user['author'] = $json['title']["\$t"];
                $user['head'] = $json['link']['2']["@href"];
                $user['status'] = $json['db:signature']["\$t"];
                $user['info'] = $json['content']["\$t"];
                break;
            case "sina":
                $this->include_SinaTOauth();
                $sina = new SinaTOauth();
                $json = $sina->VerifyCredentials($oauth_token, $oauth_token_secret);
                $json = json_decode($json, true);
                $user['token_id'] = $json['id'];
                $user['author'] = $json['screen_name'];
                $user['head'] = $json["profile_image_url"];
                $user['status'] = $json['status']["text"];
                $user['info'] = $json['description'];
                break;
            case "renren":
                $this->include_RenRenOauth();
                $renren = new RenRenOauth();
                $json = $renren->GetUsersInfo($oauth_token);
                $json = json_decode($json, true);
                $user['token_id'] = $json[0]['uid'];
                $user['author'] = $json[0]['name'];
                $user['head'] = $json[0]["headurl"];
                $user['status'] = "";
                $user['info'] = "";
                break;
        }
        return $user;
    }


    public function add_new_weibo($type, $message, $oauth_token, $oauth_token_secret)
    {
        switch($type){
            case "douban":
                $this->include_DouBanTOauth();
                $douban = new DouBanTOauth();
                $json = $douban->DoubanAdd($message, $oauth_token, $oauth_token_secret);
                break;
            case "sina":
                $this->include_SinaTOauth();
                $sina = new SinaTOauth();
                $return = $sina->SinaAdd($message,$oauth_token, $oauth_token_secret);
                $return = json_decode($return,TRUE);
                $json = $return['id'];
                break;
            case "renren":
                $this->include_RenRenOauth();
                $renren = new RenRenOauth();
                $renren->StatusUpdate($message,$oauth_token);
                break;
        }
        return $json;
    }
    
    public function getUnreadmail($type, $oauth_token, $oauth_token_secret){
        $mail = array();
        switch($type){
            case "douban":
                $this->include_DouBanTOauth();
                $douban = new DouBanTOauth();
                $json = $douban->DoubanEmail( $oauth_token, $oauth_token_secret);
                $json = json_decode($json, true);
                $mail['total'] = $json["openSearch:totalResults"]["\$t"];
                $mail['detail'] = $json["entry"];
                break;
        }
        return $mail;
    }
    
    public function getonemail($noteid,$oauth_token, $oauth_token_secret){
        $this->include_DouBanTOauth();
        $douban = new DouBanTOauth();
        $json = $douban->getDoubanEmail($noteid,$oauth_token, $oauth_token_secret);
        $json = json_decode($json, true);
        return $json;
    }
    
    public function sendmail($api,$subject,$message,$oauth_token,$oauth_token_secret ,$captcha , $captcha_token , $captcha_string ){
        $this->include_DouBanTOauth();
        $douban = new DouBanTOauth();       
        return $douban->sendDoubanEmail($api,$subject,$message,$oauth_token,$oauth_token_secret,$captcha , $captcha_token , $captcha_string);
    }

    
    //��ȡ�ҹ�ע����
    public function getMycontact($type,$uid,$oauth_token, $oauth_token_secret){
        $list = array();
        switch($type){
            case "douban":
                $this->include_DouBanTOauth();
                $douban = new DouBanTOauth();
                $json = $douban->getcontacts($uid,$oauth_token, $oauth_token_secret);
                $json = json_decode($json, true);
                //print_r($json);
                break;
        }
        return $list;
    }
    
    public function getminiblog($type,$api,$oauth_token, $oauth_token_secret){
    	switch ($type){
    		case "douban":
    				$this->include_DouBanTOauth();
    				$douban = new DouBanTOauth();
    				$json = $douban->getUserminiblog($api, $oauth_token, $oauth_token_secret);
    				$json = json_decode($json,true);
    				$return = $json[entry];
    				break;
    	}
    	
    	return $return;
    }
    
    public function bind($data = array()){
        return inserttable("oauth",$data,1);
    }
    
    public function getUserInfo($api,$type){
    	switch ($type){
    		case "douban":
    			$this->include_DouBanTOauth();
    			$douban = new DouBanTOauth();
    			return $douban->get_douban_user($api);
    	}
    }
    
    //���ͼƬ΢�� ��֧������
    public function add_pic_weibo($type, $message, $pic , $oauth_token, $oauth_token_secret){
        switch($type){
            case "sina":
                $this->include_SinaTOauth();
                $sina = new SinaTOauth();
                $sina->SinaAddPic($message , $pic ,$oauth_token, $oauth_token_secret);
                break;
        }
    }
    
	/*
	 *@param ��ȡ����
	 */    
    public function get_comment($type,$mid,$oauth_token, $oauth_token_secret){
        switch($type){
            case "douban":
               $this->include_DouBanTOauth();
               $douban = new DouBanTOauth();
               $array = $douban->comments($mid,$oauth_token, $oauth_token_secret);
               break;
            case "sina":
            	$this->include_SohuTOauth();
            	$sina = new SinaTOauth();
            	$array = $sina->commonts($mid,$oauth_token, $oauth_token_secret);
            	break;
        }
        return $array;
    }
    
    
    /**
     *@param XMLת����Ϊ����
     */
    public function XML2Array( $xml , $recursive = false )
    {
        if (!$recursive)
        {
            $array = simplexml_load_string ( $xml ) ;
        }
        else
        {
            $array = $xml ;
        }
        
        $newArray = array () ;
        $array = (array) $array ;
        foreach ($array as $key => $value )
        {
            $value = ( array ) $value ;
            if ( isset( $value [ 0 ] ) )
            {
                $newArray [ $key ] = trim ( $value [ 0 ] ) ;
            }
            else
            {
                $newArray [ $key ] = XML2Array ( $value , true ) ;
            }
        }
        return $newArray ;
    }



}
