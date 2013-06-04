<?php
/**
 * �ڸ���վ��ע�����û�
 * @author jtee<sianke731@126.com>
 * $Id: $
 *
 */
class siteUserRegister{
	var $uid = -999;
	var $username = '';
	var $password = '';
	var $email = '';
	var $groupid = -999;
	
	
	/**
	 * ��Դ��ʼ��
	 * @access public
	 * @return xwbSiteUserRegister
	 */

	function siteUserRegister(){
		global $_SGLOBAL;
		if($_SGLOBAL['closeregister']){
			showmessage('�Բ�����վ�ѹر�ע�ᣡ');	
		}
		loaducenter();
	}
	

	/**
	 * ע��һ�����ʻ�
	 * @access public
	 * @param string $name ����̳��������ϵ��û���
	 * @param string $email ����̳��������ϵ�Email
	 * @param mixed $pwd
	 * @return integer 
	 */

	function reg( $name, $email, $pwd= false ){
		global $_SCONFIG, $_SGLOBAL;
		if(strlen(trim($name))>30){
			return -1;	
		}
		$this->username = mysql_escape_string(trim($name));
		$this->email = mysql_escape_string(trim($email));
		$this->password = $pwd ? mysql_escape_string($pwd) : rand(100000,999999);
		$result_name = uc_user_checkname($this->username);
		if($result_name < 1){  //����û���
			return $result_name;
		}
		
		$result_email = uc_user_checkemail($this->email, $this->username);
		if($result_email < 1){  //�������
			return $result_email;
		}
		
		$this->uid = (int)uc_user_register($this->username, $this->password, $this->email, $this->questionid, $this->answer);
		if ($this->uid>0){
			$setarr = array(
				'uid' => $this->uid,
				'username' => $this->username,
				'password' => md5($this->uid."|".$_SGLOBAL[timestamp])//���������������
			);
			//���±����û���
			inserttable('member', $setarr, 0, true);

			//��ͨ�ռ�
			include_once(S_ROOT.'./source/function_space.php');
			$space = space_open($this->uid, $this->username, 5, $this->email);
			//Ĭ�Ϻ���
			$flog = $inserts = $fuids = $pokes = array();
			if(!empty($_SCONFIG['defaultfusername'])) {
				$query = $_SGLOBAL['db']->query("SELECT uid,username FROM ".tname('space')." WHERE username IN (".simplode(explode(',', $_SCONFIG['defaultfusername'])).")");
				while ($value = $_SGLOBAL['db']->fetch_array($query)) {
					$value = saddslashes($value);
					$fuids[] = $value['uid'];
					$inserts[] = "('".$this->uid."','$value[uid]','$value[username]','1','$_SGLOBAL[timestamp]')";
					$inserts[] = "('$value[uid]','".$this->uid."','".$this->username."','1','$_SGLOBAL[timestamp]')";
					$pokes[] = "('".$this->uid."','$value[uid]','$value[username]','".addslashes($_SCONFIG['defaultpoke'])."','$_SGLOBAL[timestamp]')";
					//��Ӻ��ѱ����¼
					$flog[] = "('$value[uid]','".$this->uid."','add','$_SGLOBAL[timestamp]')";
				}
				if($inserts) {
					$_SGLOBAL['db']->query("REPLACE INTO ".tname('friend')." (uid,fuid,fusername,status,dateline) VALUES ".implode(',', $inserts));
					$_SGLOBAL['db']->query("REPLACE INTO ".tname('poke')." (uid,fromuid,fromusername,note,dateline) VALUES ".implode(',', $pokes));
					$_SGLOBAL['db']->query("REPLACE INTO ".tname('friendlog')." (uid,fuid,action,dateline) VALUES ".implode(',', $flog));

					//��ӵ����ӱ�
					$friendstr = empty($fuids)?'':implode(',', $fuids);
					updatetable('space', array('friendnum'=>count($fuids), 'pokenum'=>count($pokes)), array('uid'=>$newuid));
					updatetable('spacefield', array('friend'=>$friendstr, 'feedfriend'=>$friendstr), array('uid'=>$newuid));

					//����Ĭ���û����ѻ���
					include_once(S_ROOT.'./source/function_cp.php');
					foreach ($fuids as $fuid) {
						friend_cache($fuid);
					}
				}
			}

			setSession($this->uid, $this->username);
			//�����¼
			if($_SCONFIG['my_status']) inserttable('userlog', array('uid'=>$newuid, 'action'=>'add', 'dateline'=>$_SGLOBAL['timestamp']), 0, true);
			
			return $this->uid;
		}else{
			return -7;
		}
	}
}