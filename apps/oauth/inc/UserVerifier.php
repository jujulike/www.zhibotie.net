<?php
class Verifier{
	/**
	 * ���������֤
	 * �뱣֤���������ַ�������̳�ַ���һ�£�������������ת���ٴ���
	 * @param string $username
	 * @param string $password
	 * @param boolen $isuid ʹ��UID��֤ô��
	 * @return array

	 *    ��һ�������±꣨$return[0]��������0�����ʾ��֤�ɹ��ĵ�¼uid������Ϊ������Ϣ��
	 *   	 -1:UC�û������ڣ����߱�ɾ��
	 *    	 -2:�����
	 *   	 -3:��ȫ���ʴ�
	 *   	 -4:�û�û����UCHOMEע��
	 *    �ڶ��������±꣨$return[1]�������ڵ���0�����ʾ��֤�ɹ���adminid��
	 *    ����Ϊ-1����ʾ��֤ʧ��
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

