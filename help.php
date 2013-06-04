<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: help.php 12059 2009-05-04 02:43:18Z liguode $
*/

include_once('./common.php');

//����rewrite
if($_SCONFIG['allowrewrite'] && isset($_GET['rewrite'])) {
	$rws = explode('-', $_GET['rewrite']);
	if($rw_uid = intval($rws[0])) {
		$_GET['uid'] = $rw_uid;
	} else {
		$_GET['do'] = $rws[0];
	}
	if(isset($rws[1])) {
		$rw_count = count($rws);
		for ($rw_i=1; $rw_i<$rw_count; $rw_i=$rw_i+2) {
			$_GET[$rws[$rw_i]] = empty($rws[$rw_i+1])?'':$rws[$rw_i+1];
		}
	}
	unset($_GET['rewrite']);
}
if($_GET['do']){
	$_GET['ac'] = $_GET['do'];
}

if(empty($_GET['ac'])) $_GET['ac'] = 'register';

$actives = array($_GET['ac'] => ' style="font-weight:bold;"');

include template('help');

?>