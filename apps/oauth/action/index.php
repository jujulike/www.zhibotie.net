<?php
//加载核心类
session_start();
include_once(S_ROOT."./apps/oauth/action/oauthConnect.php");
//配置回调地址；
$call_back_uri = "http://www.zhibotie.net/apps.php?m=oauth&a=index&op=callback";

$connect = new oauthConnect();

$op = isset($_GET['op'])?$_GET['op']:"login";
$tp = isset($_GET['orignrequest'])?$_GET['orignrequest']:"sina";

switch(strtolower($op)){
    case "login":
        $return_url = $connect->rquest_token($tp,$call_back_uri);
        header("Location:{$return_url}");
        exit();
        break;
    case "callback":
        $oauth_verifier = (string)trim($_GET['oauth_verifier']);
        $oauth_token = (string)trim($_GET['oauth_token']);
        $oauth_verifier = ($tp == "renren")?$_REQUEST["code"]:$oauth_verifier;
        if(!$oauth_verifier && !$oauth_token){
            exit(cplang("sorry_error"));
        }else{
            $access = $connect->get_access_token($tp,$oauth_verifier,$oauth_token);
            if($access){
                $rest = check_login($tp,$access);
                if($rest){
                    loaducenter();
                    //获取
                    $user = uc_get_user($rest['uid'],1);
                    uc_user_synlogin($rest['uid']);
				    setSession($user[0],$user[1]);
				    showmessage(cplang("succeed_login"),'index.php');
                }else{
                    header("Location:".getsiteurl()."apps.php?m=oauth&a=index&op=regbind&orignrequest={$tp}");
                }
            }else{
                header("Location:".getsiteurl()."apps.php?m=oauth&a=index&op=regbind&orignrequest={$tp}");
            }
        }
        break;
    case "regbind":
        $token = $_SESSION["{$tp}_access_token"];
        $token_secret = $_SESSION["{$tp}_access_token_secret"];
        if(!$_SGLOBAL['supe_uid']){
            $user = $connect->get_user_info($tp,$token,$token_secret);
            include template("connect_regsiter");
		}else{
            $rest = check_user_bind($tp,$_SGLOBAL['supe_uid']);
            if($rest){
                $user_info = $connect->get_user_info($tp,$token,$token_secret);
                updatetable("spacefield",array("spacenote"=>$user_info['status']),array("uid"=>$uid));
                $array = array();
                $array['uid'] = $_SGLOBAL['supe_uid'];
                $array['oid'] = $user_info['token_id'];
                $array['token'] = $token;
                $array['token_secret'] = $token_secret;
                $array['dateline'] = time();
                $array['type'] = $tp;
                $connect->bind($array);
                $msg = cplang("succeed_bind");
                showmessage($msg,"cp.php");
            }else{
                $msg = cplang("is_read_bind");
                showmessage($msg,"cp.php");
            }
		}
        break;
    case "regtobind":
        $bind = $_POST['bind'];
        $login_bind = $_POST['login_bind'];
        if($login_bind){
            $email = mysql_real_escape_string(trim($_POST['email']));
            $passwrod = mysql_real_escape_string(trim($_POST['password']));
            require_once S_ROOT."./apps/oauth/inc/UserVerifier.php";
            $verifyClass = new Verifier();
    		$uid = $verifyClass->verify($email, $passwrod);
            switch($uid){
    			case -1:
    				$msg = cplang("user_delete");	
                    $url = getsiteurl()."apps.php?m=oauth&a=index&op=regbind&orignrequest={$tp}";
    				break;
    			case -2:
    				$msg = cplang("user_password_error");	
                    $url = getsiteurl()."apps.php?m=oauth&a=index&op=regbind&orignrequest={$tp}";
    				break;
    			case -3:
    				$msg = cplang("user_safe_error");	
                    $url = getsiteurl()."apps.php?m=oauth&a=index&op=regbind&orignrequest={$tp}";
    				break;
    			case -4:
    				$msg = cplang("user_unreg");	
                    $url = getsiteurl()."apps.php?m=oauth&a=index&op=regbind&orignrequest={$tp}";
    				break;
    			default:
    				$token = $_SESSION["{$tp}_access_token"];
                    $token_secret = $_SESSION["{$tp}_access_token_secret"];
                    $re = check_user_bind($tp,$uid);
                    if($re){
                        $user_info = $connect->get_user_info($tp,$token,$token_secret);
                        $array = array();
                        $array['uid'] = $uid;
                        $array['oid'] = $user_info['token_id'];
                        $array['token'] = $token;
                        $array['token_secret'] = $token_secret;
                        $array['dateline'] = time();
                        $array['type'] = $tp;
       					$connect->bind($array);
       					$msg = cplang("succeed_user_ok");
                        $url = isset($_POST['refer'])?$_POST['refer']:"index.php";
                    }else{
                        $msg = cplang("user_one");
                        $url = isset($_POST['refer'])?$_POST['refer']:"index.php";
                    }
   					break;
    		}
            showmessage($msg,$url);
            exit();
        }else{
            if($bind){
                include_once(S_ROOT."./apps/oauth/inc/UserRegister.class.php");
                $regClass = new siteUserRegister();
    			$uid = $regClass->reg($bind['username'], $bind['email'], $bind['password']);
                switch($uid){
    				case -1:
    					$msg = cplang("user_name_null");
                        $url = getsiteurl()."apps.php?m=oauth&a=index&op=regbind&orignrequest={$tp}";
    					break;
    				case -2:
    					$msg = cplang("user_name_error");	
                        $url = getsiteurl()."apps.php?m=oauth&a=index&op=regbind&orignrequest={$tp}";
    					break;
    				case -3:
    					$msg = cplang("user_name_is");	
                        $url = getsiteurl()."apps.php?m=oauth&a=index&op=regbind&orignrequest={$tp}";
    					break;
    				case -4:
    					$msg = cplang("email_error");
                        $url = getsiteurl()."apps.php?m=oauth&a=index&op=regbind&orignrequest={$tp}";	
    					break;
    				case -5:
    					$msg = cplang("sys_error");
                        $url = getsiteurl()."apps.php?m=oauth&a=index&op=regbind&orignrequest={$tp}";	
    					break;
    				case -6:
    					$msg = cplang("email_is");	
                        $url = getsiteurl()."apps.php?m=oauth&a=index&op=regbind&orignrequest={$tp}";
    					break;
    				case -7:
    					$msg = cplang("none_error");	
                        $url = getsiteurl()."apps.php?m=oauth&a=index&op=regbind&orignrequest={$tp}";
    					break;
    				default:
    					$token = $_SESSION["{$tp}_access_token"];
                        $token_secret = $_SESSION["{$tp}_access_token_secret"];
                        $re = check_user_bind($tp,$uid);
                        if($re){
                            $user_info = $connect->get_user_info($tp,$token,$token_secret);
                            updatetable("spacefield",array("spacenote"=>$user_info['status']),array("uid"=>$uid));
                            $array = array();
                            $array['uid'] = $uid;
                            $array['oid'] = $user_info['token_id'];
                            $array['token'] = $token;
                            $array['token_secret'] = $token_secret;
                            $array['dateline'] = time();
                            $array['type'] = $tp;
        					$connect->bind($array);
        					$msg = cplang("succeed_user_ok");
                            $url = isset($_POST['refer'])?$_POST['refer']:"index.php";
                        }else{
                            $msg = cplang("user_one");
                            $url = isset($_POST['refer'])?$_POST['refer']:"index.php";
                        }
    					break;
    			}
                showmessage($msg,$url);
    		}
        }
        break;
    case "unbind":
        if(!$_SGLOBAL['supe_uid']){
			showmessage(cplang("need_login"),'cp.php');
		}
		unBindUser($_SGLOBAL['supe_uid'],$tp);
		showmessage(cplang("unbind"),'cp.php');
        break;
    case "loginbind":
        if(!$_SGLOBAL['supe_uid']){
			showmessage(cplang("need_login"),'cp.php');
		}
        break;
    case "getemail":
        if(!$_SGLOBAL['supe_uid']){
            echo ajax_return(0,cplang("need_login"));
            exit();
        }    
        $tp = "douban";     
        $query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("oauth")." WHERE uid='{$_SGLOBAL['supe_uid']}' AND `type`='{$tp}'");
        $array = $_SGLOBAL['db']->fetch_array($query);
        if($array){
            $mail = $connect->getUnreadmail($tp,$array['token'],$array['token_secret']);
            if($mail && $mail['detail']){
                foreach($mail['detail'] as $key => $value){
                    $newmail['author'] = mysql_real_escape_string($value['author']["name"]["\$t"]);
                    $newmail['subject'] = mysql_real_escape_string($value['title']["\$t"]);
                    $newmail['noteid'] = mysql_real_escape_string(end(explode('/',$value['link'][0]["@href"])));
                    $newmail['dateline'] = mysql_real_escape_string(strtotime($value['published']["\$t"]));
                    $lastmail[] = $newmail;
                }
                echo ajax_return(1,$lastmail);
            }else{
                echo ajax_return(0);
            }  
        }
        exit();
        break;
    case "doumail":
        $noteid = $_GET['noteid'];
        include template("apps_oauth_douomail");
        break;
    case "getonedoumail":
        //获取一封豆油
        if(!$_SGLOBAL['supe_uid']){
            echo ajax_return(0,cplang("need_login"));
            exit();
        }
        $noteid = $_GET['noteid'];
        $query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("oauth")." WHERE uid='{$_SGLOBAL['supe_uid']}' AND `type`='douban'");
        $array = $_SGLOBAL['db']->fetch_array($query);
        if($array){
            $mail = $connect->getonemail($noteid,$array['token'],$array['token_secret']);
            if($mail){
                $newmail['content'] = bbcode($mail['content']["\$t"]);
                $newmail['author']['name'] = $mail['author']['name']["\$t"];
                $newmail['author']['url'] = $mail['author']['link'][1]["@href"];
                $newmail['author']['icon'] = $mail['author']['link'][2]["@href"];
                $newmail['author']['api'] = $mail['author']['uri']["\$t"];
                $newmail['title'] = $mail['title']["\$t"];
                $newmail['dateline'] = date("Y-m-d H:i:s",strtotime($mail['published']["\$t"]));
                echo ajax_return(1,$newmail);
            }
        }        
        break;
    case "senddoumail":
        if($_SGLOBAL['supe_uid']){
            $subject = $_POST['subject'];
            $message = $_POST['message'];
            $sendapi = $_POST['sendapi'];
            $message = $message ."\n\r\t ".cplang("come_from_zhibotie");
            $query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("oauth")." WHERE uid='{$_SGLOBAL['supe_uid']}' AND `type`='douban'");
            $array = $_SGLOBAL['db']->fetch_array($query);
            $rest = $connect->sendmail($sendapi,$subject,$message,$array['token'],$array['token_secret']);
            //判断是否为403 需要验证码？
            if(preg_match("/403/",$rest)){
                preg_match("#captcha_token=(.*?)&#",$rest,$captcha);
                preg_match("/http\:\/\/www\.douban\.com(.*)/is",$rest,$m);
                $captcha_token = trim($captcha[1]);
                $captcha_url = $m[0];
                include template("apps_oauth_douomail_captcha");
                exit();
            }
            elseif(preg_match("/201/",$rest)){
                    showmessage(cplang("send_mail_ok"),"index.php");
					runlog('send_douban_mail', "SUBJECT: {$subject} ;TO:{$sendapi}; TIME:".time(), 0);
                }
        }else{
            showmessage(cplang("need_login"),"index.php");
        }
        break;
    case "mustmail":
        if($_SGLOBAL['supe_uid']){
            $subject = $_POST['subject'];
            $message = $_POST['message'];
            $sendapi = $_POST['sendapi'];
            $captcha = $_POST['captcha'];
            $captcha_string = $_POST['captcha_string'];
            $captcha_token = $_POST['captcha_token'];
            if($captcha){                
                $query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("oauth")." WHERE uid='{$_SGLOBAL['supe_uid']}' AND `type`='douban'");
                $array = $_SGLOBAL['db']->fetch_array($query);
                $rest = $connect->sendmail($sendapi,$subject,$message,$array['token'],$array['token_secret'],"true",$captcha_token,$captcha_string);
            }
            //判断是否为403 需要验证码？
            if(preg_match("/403/",$rest)){
                preg_match("#captcha_token=(.*?)&#",$rest,$captcha);
                preg_match("/http\:\/\/www\.douban\.com(.*)/is",$rest,$m);
                $captcha_token = trim($captcha[1]);
                $captcha_url = $m[0];
                include template("apps_oauth_douomail_captcha");
            }
            elseif(preg_match("/201/",$rest)){
                    showmessage(cplang("send_mail_ok"),"index.php");
                    runlog('send_douban_mail', "SUBJECT: {$subject} ;TO:{$sendapi}; TIME:".time(), 0);
                }
        }else{
            showmessage(cplang("need_login"),"index.php");
        }
        break;
    case "getmycontact":
        if($_SGLOBAL['supe_uid']){
            $query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("oauth")." WHERE uid='{$_SGLOBAL['supe_uid']}' AND `type`='douban'");
            $array = $_SGLOBAL['db']->fetch_array($query);
            if($array){
                $a = $connect->getMycontact($tp,$array['oid'],$array['token'],$array['token_secret']);
            }
        }
}


unset($connect);


function check_login($type,$data = array()){
    global $_SGLOBAL;
    $type = mysql_real_escape_string($type);
    if(is_array($data) && $data){
        $query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("oauth")." where `type` = '{$type}' AND token='".$data[$type."_access_token"]."' and token_secret='".$data[$type."_access_token_secret"]."'");
        $uid = $_SGLOBAL['db']->fetch_array($query);
        if($uid){
            return $uid;
        }else{
            return false;
        }
    }else{
        return false;
    }
}

function check_user_bind($type = "", $uid = 0){
    global $_SGLOBAL;
    $type = mysql_real_escape_string($type);
    $uid = mysql_real_escape_string($uid);
    if($type && $uid){
        $query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("oauth")." where `type` = '{$type}' AND uid='{$uid}'");
        $user = $_SGLOBAL['db']->fetch_array($query);
        if($user){
            return false;
        }else{
            return true;
        }
    }
    else{
        return false;
    }
}


	function unBindUser($uid,$type){
		global $_SGLOBAL;
		if($uid < 1){
    		return false;
    	}
        $uid = mysql_escape_string($uid);       
      
        $sql = "DELETE FROM " . tname('oauth') . " WHERE uid='".$uid."' AND    `type`='{$type}'";
        $_SGLOBAL['db']->query($sql);

	}
 

function XML2Array( $xml , $recursive = false )
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