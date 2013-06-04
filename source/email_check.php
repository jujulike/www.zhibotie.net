<?php
if ( !defined( "IN_UCHOME" ) )
{
	exit( "Access Denied" );
}
if($_SGLOBAL['supe_uid']){
	$emailcheck=getcount('spacefield',array('uid'=>$_SGLOBAL['supe_uid']),'emailcheck');
	if(!$emailcheck) {
		$url_parse_ac=$_GET['ac'];
		if(strpos(strtolower($url_parse_ac),'password')===false){
			$url_parse_op=$_GET['op'];
			if((strpos(strtolower($url_parse_op),'logout')===false)){
				showmessage('users_were_not_email_check_please_re_check','cp.php?ac=password',10);
			}
		}
	}
}
?>