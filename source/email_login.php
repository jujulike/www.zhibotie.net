<?php
	if(!defined('IN_UCHOME')) {exit('Access Denied');}
    
	ssetcookie('loginuser', $username);
	if(empty($_POST['username'])) 	showmessage('users_were_not_empty_please_re_login', 'do.php?ac='.$_SCONFIG['login_action']);
	if(!preg_match("/^[\.0-9a-z\-\_]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$/Uis",$username)) showmessage('users_were_not_email_please_re_login', 'do.php?ac='.$_SCONFIG['login_action'],5);
	$_SGLOBAL['uc_db'] = new dbstuff;
	$_SGLOBAL['uc_db']->charset = UC_DBCHARSET;	
	$_SGLOBAL['uc_db']->connect(UC_DBHOST, UC_DBUSER,UC_DBPW, UC_DBNAME, 0);	
	$sql='SELECT username FROM '.UC_DBTABLEPRE.'members WHERE email=\''.$username.'\'';	
	$query = $_SGLOBAL['uc_db']->query($sql);	
	$emailcount = mysql_num_rows($query);	
	if($emailcount>1) showmessage('users_were_notone_email_please_re_login', 'do.php?ac='.$_SCONFIG['login_action'],5);		
	$result=mysql_fetch_array($query,1);	
	$username=$result['username'];			
	unset($_SGLOBAL['uc_db']);	
?>