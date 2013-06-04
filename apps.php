<?php 
/**
 * @author 内裤叔叔
 * @param apps.php
 * @param $m string 模版动作 apps/[m]
 * @param ���� a string ���� 
 * @example apps.php?m=service&a=view 
 * @return nothing;
 * @version 0.0.1;
 * @time  2012.03.10
 * @email 294953530@qq.com
 */
header("Content-type:text/html;charset=utf-8");

include_once('./common.php');
include_once(S_ROOT."./source/class.page.php");
//����ubb�����ʽ��
include_once(S_ROOT."./source/function_bbcode.php");
include_once(S_ROOT."./source/filter.class.php");
$function = new filterInit();

//����apps��������; ������ apps/config.php;
include_once(S_ROOT.'./apps/config.php');

//����; $apps_array array();

$apps_array = array('service','oauth','wap','hovercard','push','live','commont','city','iplocation');

//���� m ģ���ȡ;
//ģ�� m �±�����index.html�����жϸ�ģ���Ƿ����;

$m = strtolower($_GET['m']);

//���� a ģ���� �û�ִ�еĶ���;
//Ĭ�ϲ��� index;
$a = !empty($_GET['a'])?$_GET['a']:"index";


if(empty($m) || !in_array($m,$apps_array) || !file_exists("./apps/{$m}/index.html")){
    exit("{$m} not undefind");    
}

//设置public js 
$public_js = "apps/{$m}";

include_once(S_ROOT."./apps/{$m}/{$apps_config[action]}{$a}.php");


/**
 * ����UCenter
 * @return obj
 */
function loaducenter() {
	require_once S_ROOT.'/uc_client/client.php';
	require_once S_ROOT.'/uc_client/model/user.php';
} 


/**
 * �û���¼
 * @param int $uid
 * @param string $username
 */
function setSession($uid, $username) {
	$setarr = array(
		'uid' => $uid,
		'username' => $username,
		'password' => md5($uid."|".time())//��������������
	);
	//����session
	include_once(S_ROOT.'./source/function_space.php');
	insertsession($setarr);
	//����cookie
	ssetcookie('auth', authcode("$setarr[password]\t$uid", 'ENCODE'), 2592000);
	ssetcookie('loginuser', $username, 31536000);
	ssetcookie('_refer', '');
}
