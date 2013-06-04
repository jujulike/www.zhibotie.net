<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: space_doing.php 12998 2009-08-05 03:29:54Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

//��ҳ
$perpage = 20;
$perpage = mob_perpage($perpage);

$page = empty($_GET['page'])?0:intval($_GET['page']);
if($page<1) $page=1;
$start = ($page-1)*$perpage;

//��鿪ʼ��
ckstart($start, $perpage);

$dolist = array();
$count = 0;

if(empty($_GET['view']) && ($space['friendnum']<$_SCONFIG['showallfriendnum'])) {
	$_GET['view'] = 'all';//Ĭ����ʾ
}
	
//�����ѯ
$f_index = '';
if($_GET['view'] == 'all') {
	
	$wheresql = "1";
	$theurl = "space.php?uid=$space[uid]&do=$do&view=all";
	$f_index = 'USE INDEX(dateline)';
	$actives = array('all'=>' class="active"');
	
} else {
	
	if(empty($space['feedfriend'])) $_GET['view'] = 'me';
	
	if($_GET['view'] == 'me') {
		$wheresql = "d.uid='$space[uid]'";
		$theurl = "space.php?uid=$space[uid]&do=$do&view=me";
		$actives = array('me'=>' class="active"');
	} else {
		$wheresql = "d.uid IN ($space[feedfriend],$space[uid])";
		$theurl = "space.php?uid=$space[uid]&do=$do&view=we";
		$f_index = 'USE INDEX(dateline)';
		$actives = array('we'=>' class="active"');
	}
}

$doid = empty($_GET['doid'])?0:intval($_GET['doid']);
if($doid) {
	$count = 1;
	$f_index = '';
	$wheresql = "doid='$doid'";
	$theurl .= "&doid=$doid";
}


$doids = $clist = $newdoids = array();
if(empty($count)) {
	$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('doing')." d WHERE $wheresql"), 0);
	//����ͳ��
	if($wheresql == "d.uid='$space[uid]'" && $space['doingnum'] != $count) {
		updatetable('space', array('doingnum' => $count), array('uid'=>$space['uid']));
	}
}
if($count) {
	$query = $_SGLOBAL['db']->query("SELECT d.*,f.id,f.path,t.id,t.subject,t.price,t.taokeurl,t.localpic FROM ".tname('doing')." d {$f_index} LEFT JOIN ".tname("fileimage")." f ON f.id=d.attachment
		LEFT JOIN ".tname("taobao")." as t ON d.taoid=t.id
		WHERE $wheresql
		ORDER BY dateline DESC
		LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		realname_set($value['uid'], $value['username']);
		$doids[] = $value['doid'];
		//echo $value['path'];
		$value['path_thumb'] = (strstr($value['path'], $_SC['attachurl']))?"{$value['path']}":"{$value['path']}!small";
		$dolist[] = $value;
	}
}

//��������
if($doid) {
	$dovalue = empty($dolist)?array():$dolist[0];
	if($dovalue) {
		if($dovalue['uid'] == $_SGLOBAL['supe_uid']) {
			$actives = array('me'=>' class="active"');
		} else {
			$space = getspace($dovalue['uid']);//�Է��Ŀռ�
			$actives = array('all'=>' class="active"');
		}
	}
}

//�ظ�
if($doids) {
	
	include_once(S_ROOT.'./source/class_tree.php');
	$tree = new tree();
	
	$values = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('docomment')." USE INDEX(dateline) WHERE doid IN (".simplode($doids).") ORDER BY dateline");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		realname_set($value['uid'], $value['username']);
		$newdoids[$value['doid']] = $value['doid'];
		if(empty($value['upid'])) {
			$value['upid'] = "do$value[doid]";
		}
		$tree->setNode($value['id'], $value['upid'], $value);
	}
}

foreach ($newdoids as $cdoid) {
	$values = $tree->getChilds("do$cdoid");
	foreach ($values as $key => $id) {
		$one = $tree->getValue($id);
		$one['layer'] = $tree->getLayer($id) * 2 - 2;
		$one['style'] = "padding-left:{$one['layer']}em;";
		if($_GET['highlight'] && $one['id'] == $_GET['highlight']) {
			$one['style'] .= 'color:red;font-weight:bold;';
		}
		$clist[$cdoid][] = $one;
	}
}

//��ҳ
$multi = multi($count, $perpage, $page, $theurl);

//ͬ�����
$moodlist = array();
if($space['mood'] && empty($start)) {
	$query = $_SGLOBAL['db']->query("SELECT s.uid,s.username,s.name,s.namestatus,s.mood,s.updatetime,s.groupid,sf.note,sf.sex
		FROM ".tname('space')." s
		LEFT JOIN ".tname('spacefield')." sf ON sf.uid=s.uid
		WHERE s.mood='$space[mood]' ORDER BY s.updatetime DESC LIMIT 0,13");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($value['uid'] != $space['uid']) {
			realname_set($value['uid'], $value['username'], $value['name'], $value['namestatus']);
			$moodlist[] = $value;
			if(count($moodlist)==12) break;
		}
	}
}

$upid = 0;

//ʵ��
realname_get();

$_TPL['css'] = 'doing';
include_once template("space_doing");

?>