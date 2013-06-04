<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: function_delete.php 13001 2009-08-05 06:18:06Z zhengqingpeng $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

//ɾ������
function deletecomments($cids) {
	global $_SGLOBAL;

	$deductcredit = array();
	$blognums = $spaces = $newcids = $dels = array();
	$allowmanage = checkperm('managecomment');
	$managebatch = checkperm('managebatch');
	$delnum = 0;
	$reward = getreward('delcomment', 0);
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('comment')." WHERE cid IN (".simplode($cids).")");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($allowmanage || $value['authorid'] == $_SGLOBAL['supe_uid'] || $value['uid'] == $_SGLOBAL['supe_uid']) {
			$dels[] = $value;
			if(!$managebatch && $value['authorid'] != $_SGLOBAL['supe_uid']) {
				$delnum++;
			}
		}
	}

	if(empty($dels) || (!$managebatch && $delnum > 1)) return array();
	
	foreach($dels as $key => $value) {
		$newcids[] = $value['cid'];
		if($value['idtype'] == 'blogid') {
			$blognums[$value['id']]++;
		}
		if($allowmanage && $value['authorid'] != $value['supe_uid']) {
			//�۳���
			$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET credit=credit-$reward[credit], experience=experience-$reward[experience] WHERE uid='$value[authorid]'");
		}
	}

	//���ɾ��
	$_SGLOBAL['db']->query("DELETE FROM ".tname('comment')." WHERE cid IN (".simplode($newcids).")");

	//ͳ�����
	$nums = renum($blognums);
	foreach ($nums[0] as $num) {
		$_SGLOBAL['db']->query("UPDATE ".tname('blog')." SET replynum=replynum-$num WHERE blogid IN (".simplode($nums[1][$num]).")");
	}
	
	return $dels;
}


//ɾ���
function deleteblogs($blogids) {
	global $_SGLOBAL;

	//��ȡ���
	$reward = getreward('delblog', 0);
	//��ȡ������Ϣ
	$spaces = $blogs = $newblogids = array();
	$allowmanage = checkperm('manageblog');
	$managebatch = checkperm('managebatch');
	$delnum = 0;
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('blog')." WHERE blogid IN (".simplode($blogids).")");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($allowmanage || $value['uid'] == $_SGLOBAL['supe_uid']) {
			$blogs[] = $value;
			if(!$managebatch && $value['uid'] != $_SGLOBAL['supe_uid']) {
				$delnum++;
			}
		}
	}
	if(empty($blogs) || (!$managebatch && $delnum > 1)) return array();
	
	foreach($blogs as $key => $value) {
		$newblogids[] = $value['blogid'];
		if($allowmanage && $value['uid'] != $_SGLOBAL['supe_uid']) {
			//�۳���
			$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET credit=credit-$reward[credit], experience=experience-$reward[experience] WHERE uid='$value[uid]'");
		}
		//tag
		$tags = array();
		$subquery = $_SGLOBAL['db']->query("SELECT tagid, blogid FROM ".tname('tagblog')." WHERE blogid='$value[blogid]'");
		while ($tag = $_SGLOBAL['db']->fetch_array($subquery)) {
			$tags[] = $tag['tagid'];
		}
		if($tags) {
			$_SGLOBAL['db']->query("UPDATE ".tname('tag')." SET blognum=blognum-1 WHERE tagid IN (".simplode($tags).")");
			$_SGLOBAL['db']->query("DELETE FROM ".tname('tagblog')." WHERE blogid='$value[blogid]'");
		}
	}

	//���ɾ��
	$_SGLOBAL['db']->query("DELETE FROM ".tname('blog')." WHERE blogid IN (".simplode($newblogids).")");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('blogfield')." WHERE blogid IN (".simplode($newblogids).")");

	//����
	$_SGLOBAL['db']->query("DELETE FROM ".tname('comment')." WHERE id IN (".simplode($newblogids).") AND idtype='blogid'");

	//ɾ��ٱ�
	$_SGLOBAL['db']->query("DELETE FROM ".tname('report')." WHERE id IN (".simplode($newblogids).") AND idtype='blogid'");

	//ɾ��feed
	$_SGLOBAL['db']->query("DELETE FROM ".tname('feed')." WHERE id IN (".simplode($newblogids).") AND idtype='blogid'");
	
	//ɾ���ӡ
	$_SGLOBAL['db']->query("DELETE FROM ".tname('clickuser')." WHERE id IN (".simplode($newblogids).") AND idtype='blogid'");

	return $blogs;
}

//ɾ���¼�
function deletefeeds($feedids) {
	global $_SGLOBAL;

	$allowmanage = checkperm('managefeed');
	$managebatch = checkperm('managebatch');
	
	$delnum = 0;
	$feeds = $newfeedids = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('feed')." WHERE feedid IN (".simplode($feedids).")");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($allowmanage || $value['uid'] == $_SGLOBAL['supe_uid']) {//����Ա/����
			$newfeedids[] = $value['feedid'];
			$newuserids[] = $value['uid'];
			$newdoingids[] = $value['id'];
			if(!$managebatch && $value['uid'] != $_SGLOBAL['supe_uid']) {
				$delnum++;
			}
			$feeds[] = $value;
		}
	}

	if(empty($newfeedids) || (!$managebatch && $delnum > 1)) return array();

	$_SGLOBAL['db']->query("DELETE FROM ".tname('feed')." WHERE feedid IN (".simplode($newfeedids).")");
	//删除动态同时删除记录
	$_SGLOBAL['db']->query("DELETE FROM ".tname('doing')." WHERE doid IN (".simplode($newdoingids).")");
	//删除动态的同时更新空间签名
	$_SGLOBAL['db']->query("update ".tname('spacefield')." SET note='',spacenote=''  WHERE uid IN (".simplode($newuserids).")");
	
	
	return $feeds;
}


//ɾ�����
function deleteshares($sids) {
	global $_SGLOBAL;

	$allowmanage = checkperm('manageshare');
	$managebatch = checkperm('managebatch');
	$delnum = 0;
	//��ȡ���
	$reward = getreward('delshare', 0);
	$spaces = $shares = $newsids = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('share')." WHERE sid IN (".simplode($sids).")");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($allowmanage || $value['uid'] == $_SGLOBAL['supe_uid']) {//����Ա/����
			$shares[] = $value;
			if(!$managebatch && $value['uid'] != $_SGLOBAL['supe_uid']) {
				$delnum++;
			}
		}
	}
	if(empty($shares) || (!$managebatch && $delnum > 1)) return array();
	
	foreach($shares as $key => $value) {
		$newsids[] = $value['sid'];
		if($allowmanage && $value['uid'] != $_SGLOBAL['supe_uid']) {
			//�۳���
			$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET credit=credit-$reward[credit], experience=experience-$reward[experience] WHERE uid='$value[uid]'");
		}
	}

	$_SGLOBAL['db']->query("DELETE FROM ".tname('share')." WHERE sid IN (".simplode($newsids).")");

	//����
	$_SGLOBAL['db']->query("DELETE FROM ".tname('comment')." WHERE id IN (".simplode($newsids).") AND idtype='sid'");
	
	//ɾ��feed
	$_SGLOBAL['db']->query("DELETE FROM ".tname('feed')." WHERE id IN (".simplode($newsids).") AND idtype='sid'");

	//ɾ��ٱ�
	$_SGLOBAL['db']->query("DELETE FROM ".tname('report')." WHERE id IN (".simplode($newsids).") AND idtype='sid'");
	
	return $shares;
}


//ɾ���¼
function deletedoings($ids) {
	global $_SGLOBAL;

	$allowmanage = checkperm('managedoing');
	$managebatch = checkperm('managebatch');
	$delnum = 0;
	//��ȡ���
	$reward = getreward('deldoing', 0);
	$spaces = $doings = $newdoids = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('doing')." WHERE doid IN (".simplode($ids).")");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($allowmanage || $value['uid'] == $_SGLOBAL['supe_uid']) {//����Ա/����
			$doings[] = $value;
			$newuserids[] = $value['uid'];
			if(!$managebatch && $value['uid'] != $_SGLOBAL['supe_uid']) {
				$delnum++;
			}
		}
	}
	
	if(empty($doings) || (!$managebatch && $delnum > 1)) return array();
	
	foreach($doings as $key => $value) {
		$newdoids[] = $value['doid'];
		if($allowmanage && $value['uid'] != $_SGLOBAL['supe_uid']) {
			//�۳���
			$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET credit=credit-$reward[credit], experience=experience-$reward[experience] WHERE uid='$value[uid]'");
		}
	}
	$_SGLOBAL['db']->query("DELETE FROM ".tname('doing')." WHERE doid IN (".simplode($newdoids).")");
	//ɾ������
	$_SGLOBAL['db']->query("DELETE FROM ".tname('docomment')." WHERE doid IN (".simplode($newdoids).")");
	
	//ɾ��feed
	$_SGLOBAL['db']->query("DELETE FROM ".tname('feed')." WHERE id IN (".simplode($newdoids).") AND idtype='doid'");
	$_SGLOBAL['db']->query("update ".tname('spacefield')." SET note='',spacenote=''  WHERE uid IN (".simplode($newuserids).")");
	
	return $doings;
}

//ɾ����
function deletethreads($tagid, $tids) {
	global $_SGLOBAL;

	$tnums = $pnums = $delthreads = $newids = $spaces = array();
	$ismanager = $allowmanage = checkperm('managethread');

	$managebatch = checkperm('managebatch');
	$delnum = 0;

	//Ⱥ��
	$wheresql = '';
	if(empty($allowmanage) && $tagid) {
		$mtag = getmtag($tagid);
		if($mtag['grade'] >=8) {
			$allowmanage = 1;
			$managebatch = 1;
			$wheresql = " AND t.tagid='$tagid'";
		}
	}
	//��ȡ���
	$reward = getreward('delthread', 0);
	$query = $_SGLOBAL['db']->query("SELECT t.* FROM ".tname('thread')." t WHERE t.tid IN(".simplode($tids).") $wheresql");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($allowmanage || $value['uid'] == $_SGLOBAL['supe_uid']) {
			$delthreads[] = $value;
			if(!$managebatch && $value['uid'] != $_SGLOBAL['supe_uid']) {
				$delnum++;
			}
		}
	}
	if(empty($delthreads) || (!$managebatch && $delnum > 1)) return array();

	foreach($delthreads as $key => $value) {
		$newids[] = $value['tid'];
		$value['isthread'] = 1;
		if($ismanager && $value['uid'] != $_SGLOBAL['supe_uid']) {
			//�۳���
			$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET credit=credit-$reward[credit], experience=experience-$reward[experience] WHERE uid='$value[uid]'");
		}
	}
	
	//ɾ��
	$_SGLOBAL['db']->query("DELETE FROM ".tname('thread')." WHERE tid IN(".simplode($newids).")");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('post')." WHERE tid IN(".simplode($newids).")");

	//ɾ��feed
	$_SGLOBAL['db']->query("DELETE FROM ".tname('feed')." WHERE id IN (".simplode($newids).") AND idtype='tid'");
	
	//ɾ��ٱ�
	$_SGLOBAL['db']->query("DELETE FROM ".tname('report')." WHERE id IN (".simplode($newids).") AND idtype='tid'");
	
	//ɾ���ӡ
	$_SGLOBAL['db']->query("DELETE FROM ".tname('clickuser')." WHERE id IN (".simplode($newids).") AND idtype='tid'");

	return $delthreads;
}

//ɾ������
function deleteposts($tagid, $pids) {
	global $_SGLOBAL;

	//ͳ��
	$postnums = $mpostnums = $tids = $delposts = $newids = $spaces = array();
	$ismanager = $allowmanage = checkperm('managethread');
	$managebatch = checkperm('managebatch');
	$delnum = 0;

	//Ⱥ��
	$wheresql = '';
	if(empty($allowmanage) && $tagid) {
		$mtag = getmtag($tagid);
		if($mtag['grade'] >=8) {
			$allowmanage = 1;
			$managebatch = 1;
			$wheresql = " AND p.tagid='$tagid'";
		}
	}
	//��ȡ���
	$reward = getreward('delcomment', 0);
	$query = $_SGLOBAL['db']->query("SELECT p.* FROM ".tname('post')." p WHERE p.pid IN (".simplode($pids).") $wheresql ORDER BY p.isthread DESC");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($allowmanage || $value['uid'] == $_SGLOBAL['supe_uid']) {
			if(!$managebatch && $value['uid'] != $_SGLOBAL['supe_uid']) {
				$delnum++;
			}
			$postarr[] = $value;
		}
	}
	if(!$managebatch && $delnum > 1) return array();
	
	foreach($postarr as $key => $value) {
		if($value['isthread']) {
			$tids[] = $value['tid'];
		} else {
			if(!in_array($value['tid'], $tids)) {
				$newids[] = $value['pid'];
				$delposts[] = $value;
				$postnums[$value['tid']]++;
				if($ismanager && $value['uid'] != $_SGLOBAL['supe_uid']) {
					//�۳���
					$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET credit=credit-$reward[credit], experience=experience-$reward[experience] WHERE uid='$value[uid]'");
				}
			}
		}
	}
	$delthreads = array();
	if($tids) {
		$delthreads = deletethreads($tagid, $tids);
	}
	if(empty($delposts)) {
		return $delthreads;
	}

	//����
	$nums = renum($postnums);
	foreach ($nums[0] as $pnum) {
		$_SGLOBAL['db']->query("UPDATE ".tname('thread')." SET replynum=replynum-$pnum WHERE tid IN (".simplode($nums[1][$pnum]).")");
	}

	//ɾ��
	$_SGLOBAL['db']->query("DELETE FROM ".tname('post')." WHERE pid IN (".simplode($newids).")");

	return $delposts;
}

//ɾ��ռ�
function deletespace($uid, $force=0) {
	global $_SGLOBAL, $_SC, $_SCONFIG;

	$delspace = array();
	$allowmanage = checkperm('managedelspace');
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('space')." WHERE uid='$uid'");
	if($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($force || $allowmanage && $value['uid'] != $_SGLOBAL['supe_uid']) {
			$delspace = $value;
			//�����ǿ��ɾ������ɾ���¼��
			if(!$force) {
				$setarr = array(
					'uid' => $value['uid'],
					'username' => saddslashes($value['username']),
					'opuid' => $_SGLOBAL['supe_uid'],
					'opusername' => $_SGLOBAL['supe_username'],
					'flag' => '-1',
					'dateline' => $_SGLOBAL['timestamp']
				);
				inserttable('spacelog', $setarr, 0, true);
			}
		}
	}
	if(empty($delspace)) return array();
	
	//�ĸ�Ȩ������
	$_SGLOBAL['usergroup'][$_SGLOBAL['member']['groupid']]['managebatch'] = 1;

	//space
	$_SGLOBAL['db']->query("DELETE FROM ".tname('space')." WHERE uid='$uid'");
	//spacefield
	$_SGLOBAL['db']->query("DELETE FROM ".tname('spacefield')." WHERE uid='$uid'");

	//feed
	$_SGLOBAL['db']->query("DELETE FROM ".tname('feed')." WHERE uid='$uid' OR (id='$uid' AND idtype='uid')");

	//��¼
	$doids =array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('doing')." WHERE uid='$uid'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$doids[$value['doid']] = $value['doid'];
	}
	
	$_SGLOBAL['db']->query("DELETE FROM ".tname('doing')." WHERE uid='$uid'");

	//ɾ���¼�ظ�
	$_SGLOBAL['db']->query("DELETE FROM ".tname('docomment')." WHERE doid IN (".simplode($doids).") OR uid='$uid'");

	//����
	$_SGLOBAL['db']->query("DELETE FROM ".tname('share')." WHERE uid='$uid'");

	//���
	$_SGLOBAL['db']->query("DELETE FROM ".tname('album')." WHERE uid='$uid'");
	
	//ɾ���ּ�¼
	$_SGLOBAL['db']->query("DELETE FROM ".tname('creditlog')." WHERE uid='$uid'");

	//ɾ��֪ͨ
	$_SGLOBAL['db']->query("DELETE FROM ".tname('notification')." WHERE (uid='$uid' OR authorid='$uid')");

	//ɾ����к�
	$_SGLOBAL['db']->query("DELETE FROM ".tname('poke')." WHERE (uid='$uid' OR fromuid='$uid')");
	
	//ɾ����ֽ���ͶƱ
	$pollid = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('poll')." WHERE uid='$uid'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$pollid[$value['pid']] = $value['pid'];
	}
	deletepolls($pollid);
	//ɾ��������ͶƱ
	$pollid = array();
	$query = $_SGLOBAL['db']->query("SELECT pid FROM ".tname('polluser')." WHERE uid='$uid'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$pollid[$value['pid']] = $value['pid'];
	}
	//�۳�ͶƱ��
	if($pollid) {
		$_SGLOBAL['db']->query("UPDATE ".tname('poll')." SET voternum=voternum-1 WHERE pid IN (".simplode($pollid).")");
	}
	$_SGLOBAL['db']->query("DELETE FROM ".tname('polluser')." WHERE uid='$uid'");
	
	//�
	$ids = array();
	$query = $_SGLOBAL['db']->query('SELECT eventid FROM '.tname('event')." WHERE uid = '$uid'");
	while($value = $_SGLOBAL['db']->fetch_array($query)) {
		$ids[] = $value['eventid'];
	}
	deleteevents($ids);
	//ɾ����μӵĻ
	$ids = $ids1 = $ids2 = array();
	$query = $_SGLOBAL['db']->query('SELECT * FROM '.tname('userevent')." WHERE uid = '$uid'");
	while($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($value['status'] == 1) {
			$ids1[] = $value['eventid'];//��ע
		} elseif($value['status'] > 1) {
			$ids2[] = $value['eventid'];//�μ�
		}
		$ids[] = $value['eventid'];
	}
	if($ids1) {
		$_SGLOBAL['db']->query('UPDATE '.tname('event').' SET follownum = follownum - 1 WHERE eventid IN ('.simplode($ids1).')');
	}
	if($ids2) {
		$_SGLOBAL['db']->query('UPDATE '.tname('event').' SET membernum = membernum - 1 WHERE eventid IN ('.simplode($ids2).')');// to to: ��û�Ҫ��鲢��ȥ��Я�������
	}
	if($ids) {
		$_SGLOBAL['db']->query('DELETE FROM '.tname('userevent').' WHERE eventid IN ('.simplode($ids).") AND uid = '$uid'");
	}
	//ɾ����ػ����
	$_SGLOBAL['db']->query('DELETE FROM '.tname('eventinvite')." WHERE uid = '$uid' OR touid = '$uid'");
	//ɾ���ϴ��ĻͼƬ
	$_SGLOBAL['db']->query('DELETE FROM '.tname('eventpic')." WHERE picid = '$uid'");//to do: ���ͬʱ���»ͼƬ��ͻ������
	
	//����
	$_SGLOBAL['db']->query('DELETE FROM '.tname('usermagic')." WHERE uid = '$uid'");
	$_SGLOBAL['db']->query('DELETE FROM '.tname('magicinlog')." WHERE uid = '$uid'");
	$_SGLOBAL['db']->query('DELETE FROM '.tname('magicuselog')." WHERE uid = '$uid'");
	
	//pic
	//ɾ��ͼƬ����
	$pics = array();
	$query = $_SGLOBAL['db']->query("SELECT filepath FROM ".tname('pic')." WHERE uid='$uid'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$pics[] = $value;
	}
	//���
	$_SGLOBAL['db']->query("DELETE FROM ".tname('pic')." WHERE uid='$uid'");

	//blog
	$blogids = array();
	$query = $_SGLOBAL['db']->query("SELECT blogid FROM ".tname('blog')." WHERE uid='$uid'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$blogids[$value['blogid']] = $value['blogid'];
		//tag
		$tags = array();
		$subquery = $_SGLOBAL['db']->query("SELECT tagid, blogid FROM ".tname('tagblog')." WHERE blogid='$value[blogid]'");
		while ($tag = $_SGLOBAL['db']->fetch_array($subquery)) {
			$tags[$tag['tagid']] = $tag['tagid'];
		}
		if($tags) {
			$_SGLOBAL['db']->query("UPDATE ".tname('tag')." SET blognum=blognum-1 WHERE tagid IN (".simplode($tags).")");
			$_SGLOBAL['db']->query("DELETE FROM ".tname('tagblog')." WHERE blogid='$value[blogid]'");
		}
	}
	//���ɾ��
	$_SGLOBAL['db']->query("DELETE FROM ".tname('blog')." WHERE uid='$uid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('blogfield')." WHERE uid='$uid'");

	//����
	$_SGLOBAL['db']->query("DELETE FROM ".tname('comment')." WHERE (uid='$uid' OR authorid='$uid' OR (id='$uid' AND idtype='uid'))");

	//�ÿ�
	$_SGLOBAL['db']->query("DELETE FROM ".tname('visitor')." WHERE (uid='$uid' OR vuid='$uid')");

	//ɾ�������¼
	$_SGLOBAL['db']->query("DELETE FROM ".tname('usertask')." WHERE uid='$uid'");

	//class
	$_SGLOBAL['db']->query("DELETE FROM ".tname('class')." WHERE uid='$uid'");

	//friend
	//����
	$_SGLOBAL['db']->query("DELETE FROM ".tname('friend')." WHERE (uid='$uid' OR fuid='$uid')");

	//member
	$_SGLOBAL['db']->query("DELETE FROM ".tname('member')." WHERE uid='$uid'");

	//ɾ���ӡ
	$_SGLOBAL['db']->query("DELETE FROM ".tname('clickuser')." WHERE uid='$uid'");

	//ɾ�����
	$_SGLOBAL['db']->query("DELETE FROM ".tname('blacklist')." WHERE (uid='$uid' OR buid='$uid')");

	//ɾ�������¼
	$_SGLOBAL['db']->query("DELETE FROM ".tname('invite')." WHERE (uid='$uid' OR fuid='$uid')");

	//ɾ���ʼ�����
	$_SGLOBAL['db']->query("DELETE FROM ".tname('mailcron').", ".tname('mailqueue')." USING ".tname('mailcron').", ".tname('mailqueue')." WHERE ".tname('mailcron').".touid='$uid' AND ".tname('mailcron').".cid=".tname('mailqueue').".cid");

	//��������
	$_SGLOBAL['db']->query("DELETE FROM ".tname('myinvite')." WHERE (touid='$uid' OR fromuid='$uid')");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('userapp')." WHERE uid='$uid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('userappfield')." WHERE uid='$uid'");

	//mtag
	//thread
	$tids = array();
	$query = $_SGLOBAL['db']->query("SELECT tid, tagid FROM ".tname('thread')." WHERE uid='$uid'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$tids[$value['tagid']][] = $value['tid'];
	}
	foreach ($tids as $tagid => $v_tids) {
		deletethreads($tagid, $v_tids);
	}

	//post
	$pids = array();
	$query = $_SGLOBAL['db']->query("SELECT pid, tagid FROM ".tname('post')." WHERE uid='$uid'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$pids[$value['tagid']][] = $value['pid'];
	}
	foreach ($pids as $tagid => $v_pids) {
		deleteposts($tagid, $v_pids);
	}
	$_SGLOBAL['db']->query("DELETE FROM ".tname('thread')." WHERE uid='$uid'");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('post')." WHERE uid='$uid'");

	//session
	$_SGLOBAL['db']->query("DELETE FROM ".tname('session')." WHERE uid='$uid'");

	//���а�
	$_SGLOBAL['db']->query("DELETE FROM ".tname('show')." WHERE uid='$uid'");

	//Ⱥ��
	$mtagids = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('tagspace')." WHERE uid='$uid'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$mtagids[$value['tagid']] = $value['tagid'];
	}
	if($mtagids) {
		$_SGLOBAL['db']->query("UPDATE ".tname('mtag')." SET membernum=membernum-1 WHERE tagid IN (".simplode($mtagids).")");
		$_SGLOBAL['db']->query("DELETE FROM ".tname('tagspace')." WHERE uid='$uid'");
	}

	$_SGLOBAL['db']->query("DELETE FROM ".tname('mtaginvite')." WHERE (uid='$uid' OR fromuid='$uid')");

	//ɾ��ͼƬ
	deletepicfiles($pics);//ɾ��ͼƬ
	
	//ɾ��ٱ�
	$_SGLOBAL['db']->query("DELETE FROM ".tname('report')." WHERE id='$uid' AND idtype='uid'");
	
	
	//����¼
	if($_SCONFIG['my_status']) inserttable('userlog', array('uid'=>$uid, 'action'=>'delete', 'dateline'=>$_SGLOBAL['timestamp']), 0, true);

	return $delspace;
}

//ɾ��ͼƬ
function deletepics($picids) {
	global $_SGLOBAL, $_SC;

	$delpics = $albumnums = $newids = $sizes = $auids = $spaces = array();
	$allowmanage = checkperm('managealbum');
	$managebatch = checkperm('managebatch');
	$delnum = 0;

	$pics = array();
	
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('pic')." WHERE picid IN (".simplode($picids).")");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($allowmanage || $value['uid'] == $_SGLOBAL['supe_uid']) {
			//ɾ���ļ�
			$pics[] = $value;
			$newids[] = $value['picid'];
			$delpics[] = $value;
			$allsize = $allsize + $value['size'];
			$sizes[$value['uid']] = $sizes[$value['uid']] + $value['size'];
			if($value['albumid']) {
				$auids[$value['albumid']] = $value['uid'];
				$albumnums[$value['albumid']]++;
			}
			if($value['uid'] != $_SGLOBAL['supe_uid']) {
				if(!$managebatch) {
					$delnum++;
				}
				$spaces[$value['uid']]++;
			}
			
		}
	}
	if(empty($delpics) || (!$managebatch && $delnum > 1)) return array();

	//��ȡ���
	$reward = getreward('delimage', 0);
	foreach ($spaces as $uid => $picnum) {
		$attachsize = intval($sizes[$uid]);
		$setsql = '';
		if($allowmanage) {
			$setsql = $reward['credit']?(",credit=credit-".($picnum*$reward['credit'])):"";
			$setsql .= $reward['experience']?(",experience=experience-".($picnum*$reward['experience'])):"";
		}
		$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET attachsize=attachsize-$attachsize $setsql WHERE uid='$uid'");
	}

	if($newids) {
		$_SGLOBAL['db']->query("DELETE FROM ".tname('pic')." WHERE picid IN (".simplode($newids).")");
		$_SGLOBAL['db']->query("DELETE FROM ".tname('comment')." WHERE id IN (".simplode($newids).") AND idtype='picid'");
		$_SGLOBAL['db']->query("DELETE FROM ".tname('feed')." WHERE id IN (".simplode($newids).") AND idtype='picid'");

		//ɾ��ٱ�
		$_SGLOBAL['db']->query("DELETE FROM ".tname('report')." WHERE id IN (".simplode($newids).") AND idtype='picid'");
			
		//ɾ���ӡ
		$_SGLOBAL['db']->query("DELETE FROM ".tname('clickuser')." WHERE id IN (".simplode($newids).") AND idtype='picid'");
	}
	if($albumnums) {
		include_once(S_ROOT.'./source/function_cp.php');
		foreach ($albumnums as $id => $num) {
			$thepic = getalbumpic($auids[$id], $id);
			$_SGLOBAL['db']->query("UPDATE ".tname('album')." SET pic='$thepic', picnum=picnum-$num WHERE albumid='$id'");
		}
	}

	//ɾ��ͼƬ
	deletepicfiles($pics);

	return $delpics;
}

//ɾ��ͼƬ�ļ�
function deletepicfiles($pics) {
	global $_SGLOBAL, $_SC;
	$remotes = array();
	foreach ($pics as $pic) {
		if($pic['remote']) {
			$remotes[] = $pic;
		} else {
			$file = $_SC['attachdir'].'./'.$pic['filepath'];
			if(!@unlink($file)) {
				runlog('PIC', "Delete pic file '$file' error.", 0);
			}
			if($pic['thumb']) {
				if(!@unlink($file.'.thumb.jpg')) {
					runlog('PIC', "Delete pic file '{$file}.thumb.jpg' error.", 0);
				}
			}
		}
	}
	//ɾ��Զ�̸���
	if($remotes) {
		include_once(S_ROOT.'./data/data_setting.php');
		include_once(S_ROOT.'./source/function_ftp.php');
		$ftpconnid = sftp_connect();
		foreach ($remotes as $pic) {
			$file = $pic['filepath'];
			if($ftpconnid) {
				if(!sftp_delete($ftpconnid, $file)) {
					runlog('FTP', "Delete pic file '$file' error.", 0);
				}
				if($pic['thumb'] && !sftp_delete($ftpconnid, $file.'.thumb.jpg')) {
					runlog('FTP', "Delete pic file '{$file}.thumb.jpg' error.", 0);
				}
			} else {
				runlog('FTP', "Delete pic file '$file' error.", 0);
				if($pic['thumb']) {
					runlog('FTP', "Delete pic file '{$file}.thumb.jpg' error.", 0);
				}
			}
		}
	}
}

//ɾ�����
function deletealbums($albumids) {
	global $_SGLOBAL, $_SC;

	$dels = $newids = $sizes = $spaces = array();
	$allowmanage = checkperm('managealbum');
	$managebatch = checkperm('managebatch');
	$delnum = 0;

	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('album')." WHERE albumid IN (".simplode($albumids).")");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($allowmanage || $value['uid'] == $_SGLOBAL['supe_uid']) {
			$dels[] = $value;
			$newids[] = $value['albumid'];
			if(!$managebatch && $value['uid'] != $_SGLOBAL['supe_uid']) {
				$delnum++;
			}
		}
	}
	if(empty($dels) || (!$managebatch && $delnum > 1)) return array();
	//��ȡ���
	$reward = getreward('delimage', 0);
	$pics = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('pic')." WHERE albumid IN (".simplode($newids).")");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$sizes[$value['uid']] = $sizes[$value['uid']] + $value['size'];
		$pics[] = $value;
		$setsql = '';
		if($allowmanage && $value['uid'] != $_SGLOBAL['supe_uid']) {
			//�۳���
			$setsql = $reward['credit']?(",credit=credit-$reward[credit]"):"";
			$setsql .= $reward['experience']?(",experience=experience-$reward[experience]"):"";
		}
		$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET attachsize=attachsize-$value[size] $setsql WHERE uid='$value[uid]'");
	}

	if($sizes) {
		$_SGLOBAL['db']->query("DELETE FROM ".tname('pic')." WHERE albumid IN (".simplode($newids).")");
	}

	$_SGLOBAL['db']->query("DELETE FROM ".tname('album')." WHERE albumid IN (".simplode($newids).")");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('feed')." WHERE id IN (".simplode($newids).") AND idtype='albumid'");
	
	//ɾ��ٱ�
	$_SGLOBAL['db']->query("DELETE FROM ".tname('report')." WHERE id IN (".simplode($newids).") AND idtype='albumid'");

	//ɾ��ͼƬ
	if($pics) {
		deletepicfiles($pics);//ɾ��ͼƬ
	}

	return $dels;
}

//ɾ��tag
function deletetags($tagids) {
	global $_SGLOBAL;
	
	if(!checkperm('managetag')) return false;

	//���
	$_SGLOBAL['db']->query("DELETE FROM ".tname('tagblog')." WHERE tagid IN (".simplode($tagids).")");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('tag')." WHERE tagid IN (".simplode($tagids).")");

	return true;
}

//ɾ��mtag
function deletemtag($tagids) {
	global $_SGLOBAL;

	if(!checkperm('manageprofield') && !checkperm('managemtag')) return array();

	$dels = $newids = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('mtag')." WHERE tagid IN (".simplode($tagids).")");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$newids[] = $value['tagid'];
		$dels[] = $value;
	}
	if(empty($newids)) return array();

	//���
	$_SGLOBAL['db']->query("DELETE FROM ".tname('tagspace')." WHERE tagid IN (".simplode($newids).")");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('mtag')." WHERE tagid IN (".simplode($newids).")");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('thread')." WHERE tagid IN (".simplode($newids).")");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('post')." WHERE tagid IN (".simplode($newids).")");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('mtaginvite')." WHERE tagid IN (".simplode($newids).")");

	//ɾ��ٱ�
	$_SGLOBAL['db']->query("DELETE FROM ".tname('report')." WHERE id IN (".simplode($newids).") AND idtype='tagid'");
	return $dels;
}

//ɾ���û���Ŀ
function deleteprofilefield($fieldids) {
	global $_SGLOBAL;

	if(!checkperm('manageprofilefield')) return false;

	//ɾ�����
	$_SGLOBAL['db']->query("DELETE FROM ".tname('profilefield')." WHERE fieldid IN (".simplode($fieldids).")");
	//��ı�ṹ
	foreach ($fieldids as $id) {
		$_SGLOBAL['db']->query("ALTER TABLE ".tname('spacefield')." DROP `field_$id`", 'SILENT');
	}

	return true;
}

//ɾ����Ŀ
function deleteprofield($fieldids, $newfieldid) {
	global $_SGLOBAL;

	if(!checkperm('manageprofield')) return false;

	//ɾ�����
	$_SGLOBAL['db']->query("DELETE FROM ".tname('profield')." WHERE fieldid IN (".simplode($fieldids).")");

	//������Ŀ
	$_SGLOBAL['db']->query("UPDATE ".tname('mtag')." SET fieldid='$newfieldid' WHERE fieldid IN (".simplode($fieldids).")");

	return true;
}

//���ɾ��
function deleteads($adids) {
	global $_SGLOBAL;

	if(!checkperm('managead')) return false;

	$dels = $newids = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('ad')." WHERE adid IN (".simplode($adids).")");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		//ɾ��ģ����ģ������ļ�
		$tpl = S_ROOT."./data/adtpl/$value[adid].htm";//ԭʼ
		swritefile($tpl, ' ');

		$newids[] = $value['adid'];
		$dels[] = $value;
	}
	if(empty($dels)) return array();

	//���
	$_SGLOBAL['db']->query("DELETE FROM ".tname('ad')." WHERE adid IN (".simplode($newids).")");

	return $dels;
}

//ģ��ɾ��
function deleteblocks($bids) {
	global $_SGLOBAL;

	if(!checkperm('managead')) return false;

	$dels = $newids = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('block')." WHERE bid IN (".simplode($bids).")");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		//ɾ��ģ����ģ������ļ�
		$tpl = S_ROOT."./data/blocktpl/$value[bid].htm";//ԭʼ
		swritefile($tpl, ' ');

		$newids[] = $value['bid'];
		$dels[] = $value;
	}
	if(empty($dels)) return array();

	//���
	$_SGLOBAL['db']->query("DELETE FROM ".tname('block')." WHERE bid IN (".simplode($newids).")");

	return $dels;
}

//ɾ������
function deletetopics($ids) {
	global $_SGLOBAL;
	
	//���
	$newids = array();
	$managetopic = checkperm('managetopic');
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('topic')." WHERE topicid IN (".simplode($ids).")");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($managetopic || $value['uid'] == $_SGLOBAL['supe_uid']) {
			$newids[] = $value['topicid'];
		}
		
	}
	if($newids) {
		$_SGLOBAL['db']->query("DELETE FROM ".tname('topic')." WHERE topicid IN (".simplode($newids).")");
		return true;
	} else {
		return false;
	}
}

//ɾ��ͶƱ
function deletepolls($pids) {
	global $_SGLOBAL;
	
	//��ȡͶƱ��Ϣ
	$sparecredit = $spaces = $polls = $newpids = array();
	//��ȡ���
	$reward = getreward('delpoll', 0);
	$allowmanage = checkperm('managepoll');
	$managebatch = checkperm('managebatch');
	$delnum = 0;
	
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('poll')." WHERE pid IN (".simplode($pids).")");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($allowmanage || $value['uid'] == $_SGLOBAL['supe_uid']) {
			$polls[] = $value;
			if(!$managebatch && $value['uid'] != $_SGLOBAL['supe_uid']) {
				$delnum++;
			}
		}
	}
	if(empty($polls) || (!$managebatch && $delnum > 1)) return array();
	
	foreach($polls as $key => $value) {
		$newpids[] = $value['pid'];
		if($allowmanage && $value['uid'] != $_SGLOBAL['supe_uid']) {
			//�۳���
			$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET credit=credit-$reward[credit], experience=experience-$reward[experience] WHERE uid='$value[uid]'");
		}
		//�黹δ������Ļ��
		if($value['credit'] > 0) {
			$sparecredit = intval($value['credit']);
			$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET credit=credit+$sparecredit WHERE uid='$value[uid]'");
		}
	}

	//���ɾ��
	$_SGLOBAL['db']->query("DELETE FROM ".tname('poll')." WHERE pid IN (".simplode($newpids).")");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('pollfield')." WHERE pid IN (".simplode($newpids).")");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('polloption')." WHERE pid IN (".simplode($newpids).")");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('polluser')." WHERE pid IN (".simplode($newpids).")");
	//����
	$_SGLOBAL['db']->query("DELETE FROM ".tname('comment')." WHERE id IN (".simplode($newpids).") AND idtype='pid'");
	
	$_SGLOBAL['db']->query("DELETE FROM ".tname('feed')." WHERE id IN (".simplode($newpids).") AND idtype='pid'");
	
	//ɾ��ٱ�
	$_SGLOBAL['db']->query("DELETE FROM ".tname('report')." WHERE id IN (".simplode($newpids).") AND idtype='pid'");
	
	return $polls;
	
}

// ɾ��
function deleteevents($eventids){
    global $_SGLOBAL;
    
	$allowmanage = checkperm('manageevent');
	$managebatch = checkperm('managebatch');
	$delnum = 0;
	$eventarr = $neweventids = $note_ids = $note_inserts = array();
    //��ȡ���
	$reward = getreward('delevent', 0);
	$query = $_SGLOBAL['db']->query("SELECT * FROM ". tname("event") . " WHERE eventid IN (" . simplode($eventids).")");
	while($value=$_SGLOBAL['db']->fetch_array($query)){
	    if($allowmanage || $value['uid'] == $_SGLOBAL['supe_uid']){
	    	$eventarr[] = $value;
		    if(!$managebatch && $value['uid'] != $_SGLOBAL['supe_uid']) {
				$delnum++;
			}
	    }
	}

	if(empty($eventarr) || (!$managebatch && $delnum > 1))    return array();
	
	foreach($eventarr as $key => $value) {
		$neweventids[] = $value['eventid'];
		// [to do: ���μ��߷�֪ͨ��������̫���������ȼ�����]
		if($value['uid'] != $_SGLOBAL['supe_uid']) {
			if($allowmanage) {
	        	//�۳���
	        	$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET credit=credit-$reward[credit], experience=experience-$reward[experience] WHERE uid='$value[uid]'");
	        }
	        $note_ids[] = $value['uid'];
			$note_msg = cplang('event_set_delete', array($value['title']));
			$note_inserts[] = "('$value[uid]', 'event', '1', '$_SGLOBAL[supe_uid]', '$_SGLOBAL[supe_username]', '".addslashes($note_msg)."', '$_SGLOBAL[timestamp]')";
		}
	}

    //���ɾ��
	$_SGLOBAL['db']->query("DELETE FROM ".tname('event')." WHERE eventid IN (".simplode($neweventids).")");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('eventpic')." WHERE eventid IN (".simplode($neweventids).")");
	$_SGLOBAL['db']->query("DELETE FROM ".tname('eventinvite')." WHERE eventid IN (".simplode($neweventids).")");

	//��û�
	$_SGLOBAL['db']->query("DELETE FROM ".tname('userevent')." WHERE eventid IN (".simplode($neweventids).")");

	//����
	$_SGLOBAL['db']->query("DELETE FROM ".tname('comment')." WHERE id IN (".simplode($neweventids).") AND idtype='eventid'");

	$_SGLOBAL['db']->query("DELETE FROM ".tname('feed')." WHERE id IN (".simplode($neweventids).") AND idtype='eventid'");
	
	//ɾ��ٱ�
	$_SGLOBAL['db']->query("DELETE FROM ".tname('report')." WHERE id IN (".simplode($neweventids).") AND idtype='eventid'");

	//����֪ͨ
	if($note_inserts){
		$_SGLOBAL['db']->query("INSERT INTO ".tname('notification')." (`uid`, `type`, `new`, `authorid`, `author`, `note`, `dateline`) VALUES ".implode(',', $note_inserts));
		$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET notenum=notenum+1 WHERE uid IN (".simplode($note_ids).")");
	}
	return $eventarr;
}

?>