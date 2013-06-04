<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: cp_profile.php 13149 2009-08-13 03:11:26Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}


if(!in_array($_GET['op'], array('base','contact','edu','work','info'))) {
	$_GET['op'] = 'base';
}


$theurl = "cp.php?ac=profile&op=$_GET[op]";

if($_GET['op'] == 'base') {
	
	if(submitcheck('profilesubmit') || submitcheck('nextsubmit')) {
		
		if(!@include_once(S_ROOT.'./data/data_profilefield.php')) {
			include_once(S_ROOT.'./source/function_cache.php');
			profilefield_cache();
		}
        
        //修改名字30天一次
        if($_POST['username'] && $_POST['username'] != $_SGLOBAL['username']){
            $modifytime = $_SGLOBAL['member']['modifytime']?$_SGLOBAL['member']['modifytime']:$_SGLOBAL['member']['dateline'];
            $mytime     = $_SGLOBAL['timestamp'];
            if(ceil($mytime - $modifytime/3600/24) > 30){
                //加载UC同步文件
                //更新UC member 
                $_SGLOBAL['db']->query("UPDATE ".UC_DBTABLEPRE."members SET username='$_POST[username]' WHERE uid='$space[uid]'");
                //更新主数据库 member表
                $_SGLOBAL['db']->query("UPDATE ".tname("member")." SET username='$_POST[username]' WHERE uid='$space[uid]'");
                //更新BLOG
                $_SGLOBAL['db']->query("UPDATE ".tname("blog")." SET username='$_POST[username]' WHERE uid='$space[uid]'");
                //更新相册
                $_SGLOBAL['db']->query("UPDATE ".tname("album")." SET username='$_POST[username]' WHERE uid='$space[uid]'");
                //更新微博
                $_SGLOBAL['db']->query("UPDATE ".tname("doing")." SET username='$_POST[username]' WHERE uid='$space[uid]'");
                //更新活动
                $_SGLOBAL['db']->query("UPDATE ".tname("event")." SET username='$_POST[username]' WHERE uid='$space[uid]'");
                //更新feed表
                $_SGLOBAL['db']->query("UPDATE ".tname("feed")." SET username='$_POST[username]' WHERE uid='$space[uid]'");
                //更新space表
                $_SGLOBAL['db']->query("UPDATE ".tname("space")." SET username='$_POST[username]',modifytime='$mytime' WHERE uid='$space[uid]'");
                //include_once(S_ROOT.'./uc_client/client.php');
                //uc_user_edit($_POST['username']);
                //更新 $_SGLOBAL
                $_SGLOBAL['username'] =  $_POST['username'];
                $_SGLOBAL[member]['username'] = $_POST['username'];   
            }else{
                showmessage('30days_no_modify',"cp.php");
            }         
            //lastlogin
        }
        
		$profilefields = empty($_SGLOBAL['profilefield'])?array():$_SGLOBAL['profilefield'];
	
		//�ύ���
		$setarr = array(
			'birthyear' => intval($_POST['birthyear']),
			'birthmonth' => intval($_POST['birthmonth']),
			'birthday' => intval($_POST['birthday']),
			'blood' => getstr($_POST['blood'], 5, 1, 1),
			'marry' => intval($_POST['marry']),
			'birthprovince' => getstr($_POST['birthprovince'], 20, 1, 1),
			'birthcity' => getstr($_POST['birthcity'], 20, 1, 1),
			'birtharea' => getstr($_POST['birtharea'], 20, 1, 1),
			'resideprovince' => getstr($_POST['resideprovince'], 20, 1, 1),
			'residecity' => getstr($_POST['residecity'], 20, 1, 1),
			'residearea' => getstr($_POST['residearea'], 20, 1, 1),
		);
		
		//�Ա�
		$_POST['sex'] = intval($_POST['sex']);
		if($_POST['sex'] && empty($space['sex'])) $setarr['sex'] = $_POST['sex'];
	
		foreach ($profilefields as $field => $value) {
			if($value['formtype'] == 'select') $value['maxsize'] = 255;
			$setarr['field_'.$field] = getstr($_POST['field_'.$field], $value['maxsize'], 1, 1);
			if($value['required'] && empty($setarr['field_'.$field])) {
				showmessage('field_required', '', 1, array($value['title']));
			}
		}
		
		if(($setarr["field_1"] == "直男" || $setarr[field_1] == "腐男") || ($setarr["field_2"] == "直女" || $setarr["field_2"] == "腐女")){
			$setarr["field_3"] == "";
		}
		
		updatetable('spacefield', $setarr, array('uid'=>$_SGLOBAL['supe_uid']));
		
		//��˽
		$inserts = array();
		foreach ($_POST['friend'] as $key => $value) {
			$value = intval($value);
			$inserts[] = "('base','$key','$space[uid]','$value')";
		}
		if($inserts) {
			$_SGLOBAL['db']->query("DELETE FROM ".tname('spaceinfo')." WHERE uid='$space[uid]' AND type='base'");
			$_SGLOBAL['db']->query("INSERT INTO ".tname('spaceinfo')." (type,subtype,uid,friend)
				VALUES ".implode(',', $inserts));
		}

		//����ʵ��
		$setarr = array(
			'name' => getstr($_POST['name'], 10, 1, 1, 1),
			'namestatus' => $_SCONFIG['namecheck']?0:1
		);
		if(checkperm('managename')) {
			 $setarr['namestatus'] = 1;
		}
	
		if($setarr['name'] && strlen($setarr['name']) < 4) {//����С��4���ַ�			
			showmessage('realname_too_short');
		}
		if($setarr['name'] != $space['name'] || $setarr['namestatus']) {
			
			//��һ����дʵ��
			if($_SCONFIG['realname'] && empty($space['name']) &&  $setarr['name'] != $space['name'] && $setarr['namestatus']) {
				$reward = getreward('realname', 0);
				if($reward['credit']) {
					$setarr['credit'] = $space['credit'] + $reward['credit'];
				}
				if($reward['experience']) {
					$setarr['experience'] = $space['experience'] + $reward['experience'];
				}
			
			} elseif($_SCONFIG['realname'] && $space['namestatus'] && !checkperm('managename')) {	//�ۼ����
				$reward = getreward('editrealname', 0);
				//���
				if($space['name'] && $setarr['name'] != $space['name'] && ($reward['credit'] || $reward['experience'])) {
					//��֤����ֵ
					if($space['experience'] >= $reward['experience']) {
						$setarr['experience'] = $space['experience'] - $reward['experience'];
					} else {
						showmessage('experience_inadequate', '', 1, array($space['experience'], $reward['experience']));
					}
				
					if($space['credit'] >= $reward['credit']) {
						$setarr['credit'] = $space['credit'] - $reward['credit'];
					} else {
						showmessage('integral_inadequate', '', 1, array($space['credit'],  $reward['credit']));
					}
				}
			}
			updatetable('space', $setarr, array('uid'=>$_SGLOBAL['supe_uid']));
		}
	
		//����¼
		if($_SCONFIG['my_status']) {
			inserttable('userlog', array('uid'=>$_SGLOBAL['supe_uid'], 'action'=>'update', 'dateline'=>$_SGLOBAL['timestamp'], 'type'=>0), 0, true);
		}
		
		//����feed
		if(ckprivacy('profile', 1)) {
			feed_add('profile', cplang('feed_profile_update_base'));
		}
	
		if(submitcheck('nextsubmit')) {
			$url = 'cp.php?ac=profile&op=contact';
		} else {
			$url = 'cp.php?ac=profile&op=base';
		}
		showmessage('update_on_successful_individuals', $url);
	}

	//�Ա�
	$sexarr = array($space['sex']=>' checked');
	
	//����:��
	$birthyeayhtml = '';
	$nowy = sgmdate('Y');
	for ($i=0; $i<100; $i++) {
		$they = $nowy - $i;
		$selectstr = $they == $space['birthyear']?' selected':'';
		$birthyeayhtml .= "<option value=\"$they\"$selectstr>$they</option>";
	}
	//����:��
	$birthmonthhtml = '';
	for ($i=1; $i<13; $i++) {
		$selectstr = $i == $space['birthmonth']?' selected':'';
		$birthmonthhtml .= "<option value=\"$i\"$selectstr>$i</option>";
	}
	//����:��
	$birthdayhtml = '';
	for ($i=1; $i<32; $i++) {
		$selectstr = $i == $space['birthday']?' selected':'';
		$birthdayhtml .= "<option value=\"$i\"$selectstr>$i</option>";
	}
	//Ѫ��
	$bloodhtml = '';
	foreach (array('A','B','O','AB') as $value) {
		$selectstr = $value == $space['blood']?' selected':'';
		$bloodhtml .= "<option value=\"$value\"$selectstr>$value</option>";
	}
	//����
	$marryarr = array($space['marry'] => ' selected');
	
	//��Ŀ�?
	$profilefields = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('profilefield')." ORDER BY displayorder");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$fieldid = $value['fieldid'];
		$value['formhtml'] = '';
	
		if($value['formtype'] == 'text') {
			$value['formhtml'] = "<input type=\"text\" name=\"field_$fieldid\"  value=\"".$space["field_$fieldid"]."\" class=\"t_input\">";
		} else {
            $value['formhtml'] .= "<select name=\"field_$fieldid\" id=\"field_$fieldid\">";
			if(empty($value['required'])) {
				$value['formhtml'] .= "<option value=\"\"></option>";
			}
			$optionarr = explode("\n", $value['choice']);
			foreach ($optionarr as $ov) {
				$ov = trim($ov);
				if($ov) {
					$selectstr = $space["field_$fieldid"]==$ov?' selected':'';
					$value['formhtml'] .= "<option value=\"$ov\"$selectstr>$ov</option>";
				}
			}
			$value['formhtml'] .= "</select>";
		}
	
		$profilefields[$value['fieldid']] = $value;
	}
	
	if(empty($_SCONFIG['namechange'])) {
		$_GET['namechange'] = 0;//�������޸�
	}
	
	//��˽
	$friendarr = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('spaceinfo')." WHERE uid='$space[uid]' AND type='base'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$friendarr[$value['subtype']][$value['friend']] = ' selected';
	}
	
} elseif ($_GET['op'] == 'contact') {
	
	if($_GET['resend']) {
		//���·���������֤
		$toemail = $space['newemail']?$space['newemail']:$space['email'];
		emailcheck_send($space['uid'], $toemail);
		showmessage('do_success', "cp.php?ac=profile&op=contact");
	}
	
	if(submitcheck('profilesubmit') || submitcheck('nextsubmit')) {
		//�ύ���
		$setarr = array(
			'mobile' => getstr($_POST['mobile'], 40, 1, 1),
			'qq' => getstr($_POST['qq'], 20, 1, 1),
			'msn' => getstr($_POST['msn'], 80, 1, 1),
		);
		
		//��������
		$newemail = isemail($_POST['email'])?$_POST['email']:'';
		if(isset($_POST['email']) && $newemail != $space['email']) {
			
			//�������Ψһ��
			if($_SCONFIG['uniqueemail']) {
				if(getcount('spacefield', array('email'=>$newemail, 'emailcheck'=>1))) {
					showmessage('uniqueemail_check');
				}
			}
			
			//��֤����
			if(!$passport = getpassport($_SGLOBAL['supe_username'], $_POST['password'])) {
				showmessage('password_is_not_passed');
			}
			
			//�����޸�
			if(empty($newemail)) {
				//����ɾ��
				$setarr['email'] = '';
				$setarr['emailcheck'] = 0;
			} elseif($newemail != $space['email']) {
				//֮ǰ�Ѿ���֤
				if($space['emailcheck']) {
					//�����ʼ���֤�����޸�����
					$setarr['newemail'] = $newemail;
				} else {
					//�޸�����
					$setarr['email'] = $newemail;
				}
				emailcheck_send($space['uid'], $newemail);
			}
		}
		
		updatetable('spacefield', $setarr, array('uid'=>$_SGLOBAL['supe_uid']));
		
		//��˽
		$inserts = array();
		foreach ($_POST['friend'] as $key => $value) {
			$value = intval($value);
			$inserts[] = "('contact','$key','$space[uid]','$value')";
		}
		if($inserts) {
			$_SGLOBAL['db']->query("DELETE FROM ".tname('spaceinfo')." WHERE uid='$space[uid]' AND type='contact'");
			$_SGLOBAL['db']->query("INSERT INTO ".tname('spaceinfo')." (type,subtype,uid,friend)
				VALUES ".implode(',', $inserts));
		}

		//����¼
		if($_SCONFIG['my_status']) {
			inserttable('userlog', array('uid'=>$_SGLOBAL['supe_uid'], 'action'=>'update', 'dateline'=>$_SGLOBAL['timestamp'], 'type'=>2), 0, true);
		}
		
		//����feed
		if(ckprivacy('profile', 1)) {
			feed_add('profile', cplang('feed_profile_update_contact'));
		}
		
		if(submitcheck('nextsubmit')) {
			$url = 'cp.php?ac=profile&op=edu';
		} else {
			$url = 'cp.php?ac=profile&op=contact';
		}
		showmessage('update_on_successful_individuals', $url);
	}
	
	//��˽
	$friendarr = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('spaceinfo')." WHERE uid='$space[uid]' AND type='contact'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$friendarr[$value['subtype']][$value['friend']] = ' selected';
	}
	
} elseif ($_GET['op'] == 'edu') {
	
	if($_GET['subop'] == 'delete') {
		$infoid = intval($_GET['infoid']);
		if($infoid) {
			$_SGLOBAL['db']->query("DELETE FROM ".tname('spaceinfo')." WHERE infoid='$infoid' AND uid='$space[uid]' AND type='edu'");
		}
	}
	
	if(submitcheck('profilesubmit') || submitcheck('nextsubmit')) {
		//�ύ���
		$inserts = array();
		foreach ($_POST['title'] as $key => $value) {
			$value = getstr($value, 100, 1, 1);
			if($value) {
				$subtitle= getstr($_POST['subtitle'][$key], 20, 1, 1);
				$startyear = intval($_POST['startyear'][$key]);
				$friend = intval($_POST['friend'][$key]);
				$inserts[] = "('$space[uid]','edu','$value','$subtitle','$startyear','$friend')";
			}
		}
		if($inserts) {
			$_SGLOBAL['db']->query("INSERT INTO ".tname('spaceinfo')."(uid,type,title,subtitle,startyear,friend) VALUES ".implode(',', $inserts));
		}
		
		//����¼
		if($_SCONFIG['my_status']) {
			inserttable('userlog', array('uid'=>$_SGLOBAL['supe_uid'], 'action'=>'update', 'dateline'=>$_SGLOBAL['timestamp'], 'type'=>2), 0, true);
		}
		
		//����feed
		if(ckprivacy('profile', 1)) {
			feed_add('profile', cplang('feed_profile_update_edu'));
		}

		if(submitcheck('nextsubmit')) {
			$url = 'cp.php?ac=profile&op=work';
		} else {
			$url = 'cp.php?ac=profile&op=edu';
		}
		showmessage('update_on_successful_individuals', $url);
	}
	
	//��ǰ�Ѿ����õ�ѧУ
	$list = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('spaceinfo')." WHERE uid='$space[uid]' AND type='edu'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$value['title_s'] = urlencode($value['title']);
		$list[] = $value;
	}
	
} elseif ($_GET['op'] == 'work') {
	
	
	if($_GET['subop'] == 'delete') {
		$infoid = intval($_GET['infoid']);
		if($infoid) {
			$_SGLOBAL['db']->query("DELETE FROM ".tname('spaceinfo')." WHERE infoid='$infoid' AND uid='$space[uid]' AND type='work'");
		}
	}
	
	if(submitcheck('profilesubmit') || submitcheck('nextsubmit')) {
		//�ύ���
		$inserts = array();
		foreach ($_POST['title'] as $key => $value) {
			$value = getstr($value, 100, 1, 1);
			if($value) {
				$subtitle= getstr($_POST['subtitle'][$key], 20, 1, 1);
				$startyear = intval($_POST['startyear'][$key]);
				$startmonth = intval($_POST['startmonth'][$key]);
				$endyear = intval($_POST['endyear'][$key]);
				$endmonth = $endyear?intval($_POST['endmonth'][$key]):0;
				$friend = intval($_POST['friend'][$key]);
				$inserts[] = "('$space[uid]','work','$value','$subtitle','$startyear','$startmonth','$endyear','$endmonth','$friend')";
			}
		}
		if($inserts) {
			$_SGLOBAL['db']->query("INSERT INTO ".tname('spaceinfo')."
				(uid,type,title,subtitle,startyear,startmonth,endyear,endmonth,friend)
				VALUES ".implode(',', $inserts));
		}

		//����¼
		if($_SCONFIG['my_status']) {
			inserttable('userlog', array('uid'=>$_SGLOBAL['supe_uid'], 'action'=>'update', 'dateline'=>$_SGLOBAL['timestamp'], 'type'=>2), 0, true);
		}
		
		//����feed
		if(ckprivacy('profile', 1)) {
			feed_add('profile', cplang('feed_profile_update_work'));
		}


		if(submitcheck('nextsubmit')) {
			$url = 'cp.php?ac=profile&op=info';
		} else {
			$url = 'cp.php?ac=profile&op=work';
		}
		showmessage('update_on_successful_individuals', $url);
	}
	
	//��ǰ�Ѿ�����
	$list = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('spaceinfo')." WHERE uid='$space[uid]' AND type='work'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$value['title_s'] = urlencode($value['title']);
		$list[] = $value;
	}
	
} elseif ($_GET['op'] == 'info') {
	
	if(submitcheck('profilesubmit')) {
		
		$inserts = array();
		foreach ($_POST['info'] as $key => $value) {
			$value = getstr($value, 500, 1, 1);
			$friend = intval($_POST['info_friend'][$key]);
			$inserts[] = "('$space[uid]','info','$key','$value','$friend')";
		}
		
		if($inserts) {
			$_SGLOBAL['db']->query("DELETE FROM ".tname('spaceinfo')." WHERE uid='$space[uid]' AND type='info'");
			$_SGLOBAL['db']->query("INSERT INTO ".tname('spaceinfo')."
				(uid,type,subtype,title,friend)
				VALUES ".implode(',', $inserts));
		}
	
		//����¼
		if($_SCONFIG['my_status']) {
			inserttable('userlog', array('uid'=>$_SGLOBAL['supe_uid'], 'action'=>'update', 'dateline'=>$_SGLOBAL['timestamp'], 'type'=>2), 0, true);
		}
		
		//����feed
		if(ckprivacy('profile', 1)) {
			feed_add('profile', cplang('feed_profile_update_info'));
		}


		$url = 'cp.php?ac=profile&op=info';
		showmessage('update_on_successful_individuals', $url);
	}
	
	//��˽
	$list = $friends = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('spaceinfo')." WHERE uid='$space[uid]' AND type='info'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$list[$value['subtype']] = $value;
		$friends[$value['subtype']][$value['friend']] = ' selected';
	}
	
}

$cat_actives = array($_GET['op'] => ' class="active"');


if($_GET['op'] == 'edu' || $_GET['op'] == 'work') {
	$yearhtml = '';
	$nowy = sgmdate('Y');
	for ($i=0; $i<50; $i++) {
		$they = $nowy - $i;
		$yearhtml .= "<option value=\"$they\">$they</option>";
	}
	
	$monthhtml = '';
	for ($i=1; $i<13; $i++) {
		$monthhtml .= "<option value=\"$i\">$i</option>";
	}
}

include template("cp_profile");

?>