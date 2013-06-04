<?php
session_start();

include_once(S_ROOT."./apps/oauth/inc/sina.class.php");

$option = isset($_GET['option'])?strtolower($_GET['option']):"oauth";

//实例化新浪授权类
$sina = new sinaClass();

//开始处理新浪授权内容；
switch ($option){
	case "oauth":
		$uri = $sina->oauth_url();
		if($uri) header("Location:{$uri}");
		else exit("sorry");
		break;
	case "callback":
		//开始获取CODE
		$keys = array();
		$keys['code'] = $_GET['code'];
		$keys['redirect_uri'] = $sina->get_callback();
		$token = $sina->access_token($keys);
		//把$token 写入 session 环境变量中，以后方便取用以及防止新浪将token拒绝
		$_SESSION['token'] = json_encode($token);
		//序列化token 
		header("Location:apps.php?m=oauth&a=sina&option=bind");
		break;
	case "bind":
		$sina_token = json_decode($_SESSION['token'],true);
		//判断是否登录
		if($_SGLOBAL['supe_uid']){
			//登录直接绑定
			$sql = "SELECT * FROM ".tname("oauth")." WHERE type='sina' AND uid='{$_SGLOBAL['supe_uid']}' LIMIT 1";
			$query = $_SGLOBAL['db']->query($sql);
			$data = $_SGLOBAL['db']->fetch_array($query);
			if($data && $data['uid'] == $_SGLOBAL['supe_uid'] && $data['oid'] == $sina_token['uid']){
				updatetable("oauth",array("token"=>$sina_token['access_token'],"token_secret"=>$sina_token['access_token']),array("id"=>$data['id']));
				$msg = cplang("succeed_bind");
				showmessage($msg,"cp.php");
			}else{
				header("Location:apps.php?m=oauth&a=sina&option=regbind");
				exit();
			}
		}else{
			//检测是否通过登录
			$sql = "SELECT * FROM ".tname("oauth")." WHERE type='sina' AND oid='{$sina_token['uid']}' LIMIT 1";
			$query = $_SGLOBAL['db']->query($sql);
			$data = $_SGLOBAL['db']->fetch_array($query);
			if($data && $data['uid']){
				//开始登录
				loaducenter();
				$user = uc_get_user($data['uid'],1);
				uc_user_synlogin($data['uid']);
				setSession($user[0],$user[1]);
				//同步升级token
				updatetable("oauth",array("token"=>$sina_token['access_token'],"token_secret"=>$sina_token['access_token']),array("id"=>$data['id']));
				showmessage(cplang("succeed_login"),'index.php');
			}else{
				header("Location:apps.php?m=oauth&a=sina&option=regbind");
				exit();
			}
		}
		break;
	case "regbind":
		$from = "sina_v2";
		$sina_token = json_decode($_SESSION['token'],true);
		$user = $sina->get_user_info($sina_token['access_token'], $sina_token['uid']);
		$user['author'] = $user['screen_name'];
		include template("connect_regsiter");
		break;
	case "regtobind":
		$bind = $_POST['bind'];
		$login_bind = $_POST['login_bind'];
		$sina_token = json_decode($_SESSION['token'],true);
		if($login_bind){
			$email = mysql_real_escape_string(trim($_POST['email']));
			$passwrod = mysql_real_escape_string(trim($_POST['password']));
			require_once S_ROOT."./apps/oauth/inc/UserVerifier.php";
			$verifyClass = new Verifier();
			$uid = $verifyClass->verify($email, $passwrod);
			switch($uid){
				case -1:
					$msg = cplang("user_delete");
					$url = getsiteurl()."apps.php?m=oauth&a=sina&option=regbind";
					break;
				case -2:
					$msg = cplang("user_password_error");
					$url = getsiteurl()."apps.php?m=oauth&a=sina&option=regbind";
					break;
				case -3:
					$msg = cplang("user_safe_error");
					$url = getsiteurl()."apps.php?m=oauth&a=sina&option=regbind";
					break;
				case -4:
					$msg = cplang("user_unreg");
					$url = getsiteurl()."apps.php?m=oauth&a=sina&option=regbind";
					break;
				default:
					$sina_token = json_decode($_SESSION['token'],true);
					$re = check_user_bind("sina",$uid);
					if($re){
						$user_info = $sina->get_user_info($sina_token['access_token'], $sina_token['uid']);
						$array = array();
						$array['uid'] = $uid;
						$array['oid'] = $sina_token['uid'];
						$array['token'] = $sina_token['access_token'];
						$array['token_secret'] = $sina_token['access_token'];
						$array['dateline'] = time();
						$array['type'] = "sina";
						inserttable("oauth", $array,1);
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
						$url = getsiteurl()."apps.php?m=oauth&a=sina&option=regbind";
						break;
					case -2:
						$msg = cplang("user_name_error");
						$url = getsiteurl()."apps.php?m=oauth&a=sina&option=regbind";
						break;
					case -3:
						$msg = cplang("user_name_is");
						$url = getsiteurl()."apps.php?m=oauth&a=sina&option=regbind";
						break;
					case -4:
						$msg = cplang("email_error");
						$url = getsiteurl()."apps.php?m=oauth&a=sina&option=regbind";
						break;
					case -5:
						$msg = cplang("sys_error");
						$url = getsiteurl()."apps.php?m=oauth&a=sina&option=regbind";
						break;
					case -6:
						$msg = cplang("email_is");
						$url = getsiteurl()."apps.php?m=oauth&a=sina&option=regbind";
						break;
					case -7:
						$msg = cplang("none_error");
						$url = getsiteurl()."apps.php?m=oauth&a=sina&option=regbind";
						break;
					default:
						$sina_token = json_decode($_SESSION['token'],true);
						$re = check_user_bind("sina",$uid);
						if($re){
							$user_info = $sina->get_user_info($sina_token['access_token'], $sina_token['uid']);
							$array = array();
							$array['uid'] = $uid;
							$array['oid'] = $sina_token['uid'];
							$array['token'] = $sina_token['access_token'];
							$array['token_secret'] = $sina_token['access_token'];
							$array['dateline'] = time();
							$array['type'] = "sina";
							updatetable("spacefield",array("spacenote"=>$user_info['description']),array("uid"=>$uid));
							inserttable("oauth", $array ,1);
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
		unBindUser($_SGLOBAL['supe_uid'],"sina");
		showmessage(cplang("unbind"),'cp.php');
		break;
}


function check_user_bind($type = "", $uid = 0){
	global $_SGLOBAL;
	$type = mysql_real_escape_string($type);
	$uid = mysql_real_escape_string($uid);
	if($type && $uid){
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("oauth")." where `type` = '{$type}' AND uid='{$uid}'");
		$user = $_SGLOBAL['db']->fetch_array($query);
		if($user){
			if($user['type'] == "sina" && $user['token'] == $user['token_secret']){
				return false;
			}else{
				return true;
			}
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