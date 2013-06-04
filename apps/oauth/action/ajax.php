<?php

/**
 * @author 内裤叔叔
 * @copyright 2012
 */
$option = $_GET['option'];

switch($option){
    case "regcheck_mail":
        $email = mysql_real_escape_string($_POST['email']);
        loaducenter();
        $return = uc_user_checkemail($email);
        switch($return){
            case 1:
                $message = " 该 email 未被注册";
                break;
            case -4 :
                $message = " 该 email 格式有误";
                 break;
            case -5 : 
                $message = " 该 email 不允许注册";
                 break;
            case -6 : 
                $message = " 该 email 已经被注册";
                break;                
        }
        echo ajax_return($return,$message);
        break;
    case "bind_check":
        $email = mysql_real_escape_string(trim($_GET['email']));
        $passwrod = mysql_real_escape_string(trim($_GET['password']));
        require_once S_ROOT."./apps/oauth/inc/UserVerifier.php";
        $verifyClass = new Verifier();
		$uid = $verifyClass->verify($email, $passwrod);
        switch($uid){
			case -1:
				$msg = '用户不存在或者被删除';	
				break;
			case -2:
				$msg = '密码错误';	
				break;
			case -3:
				$msg = '安全提问错';	
				break;
			case -4:
				$msg = '用户没有在UCHOME注册';	
				break;
			default:
				$msg = 'ok';
                break;
		}
        echo ajax_return($uid,$msg);
        break;
        
}