<?php
class Verifier{
	/**
	 * 进行身份验证
	 * 请保证传参所用字符集和论坛字符集一致，否则请先自行转换再传参
	 * @param string $username
	 * @param string $password
	 * @param boolen $isuid 使用UID验证么？
	 * @return array

	 *    第一个数组下标（$return[0]）若大于0，则表示验证成功的登录uid。否则为错误信息：
	 *   	 -1:UC用户不存在，或者被删除
	 *    	 -2:密码错
	 *   	 -3:安全提问错
	 *   	 -4:用户没有在UCHOME注册
	 *    第二个数组下标（$return[1]）若大于等于0，则表示验证成功的adminid；
	 *    否则为-1，表示验证失败
	 */
	public function verify( $username, $password,$isuid = 0 ){
		global $_SGLOBAL;
		$return = -1;
        $passwordmd5 = preg_match('/^\w{32}$/', $password) ? $password : md5($password);
        $user = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query("SELECT * FROM `uc_members` WHERE email='$username'"));
        if($user){
            if($user['password'] == md5($passwordmd5.$user['salt'])){
                $member =$_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query("SELECT uid, username
												FROM uchome_member
												WHERE uid='$user[uid]'"));
                if(!$member){
                    $return = -4;
                }else{
                    setSession($member['uid'],$member['username'] );
				    $return = $member['uid'];
                }
            }
        }else{
            $return = -1;
        }
		return $return;
	}
}

