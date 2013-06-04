<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: space_feed.php 13194 2009-08-18 07:44:40Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}


//��ʾȫվ��̬�ĺ�����
if(empty($_SCONFIG['showallfriendnum']) || $_SCONFIG['showallfriendnum']<1) $_SCONFIG['showallfriendnum'] = 10;
//Ĭ���ȵ�����
if(empty($_SCONFIG['feedhotday'])) $_SCONFIG['feedhotday'] = 2;

//��վ���
$isnewer = $space['friendnum']<$_SCONFIG['showallfriendnum']?1:0;
if(empty($_GET['view']) && $space['self'] && $isnewer && !isset($_GET['view'])) {
	$_GET['view'] = 'all';//Ĭ����ʾ
}

if($_GET['view'] != 'douban'){
	//��ҳ
	$perpage = $_SCONFIG['feedmaxnum']<50?50:$_SCONFIG['feedmaxnum'];
	$perpage = mob_perpage($perpage);
	
	if($_GET['view'] == 'hot') {
		$perpage = 50;
	}
	
	$start = empty($_GET['start'])?0:intval($_GET['start']);
	//��鿪ʼ��
	ckstart($start, $perpage);
	
	//����ʱ�俪ʼ��
	$_SGLOBAL['today'] = sstrtotime(sgmdate('Y-m-d'));
	
	//�����ȶ�
	$minhot = $_SCONFIG['feedhotmin']<1?3:$_SCONFIG['feedhotmin'];
	$_SGLOBAL['gift_appid'] = '1027468';
	
	if($_GET['view'] == 'all') {
	
		$wheresql = "1";//û����˽
		$ordersql = "dateline DESC";
		$theurl = "space.php?uid=$space[uid]&do=$do&view=all";
		$f_index = '';
	
	} elseif($_GET['view'] == 'hot') {
	
		$wheresql = "hot>='$minhot'";
		$ordersql = "dateline DESC";
		$theurl = "space.php?uid=$space[uid]&do=$do&view=hot";
		$f_index = '';
	
	}elseif($_GET['view'] == 'tags'){
		//话题
	    $_GET['tags'] = isset($_GET['tags'])?trim($_GET['tags']):"";
	    if($_GET['tags']){
	        $query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("doingtie") . " WHERE `hash`='".md5(trim($_GET['tags']))."'");
	        $res = $_SGLOBAL['db']->fetch_array($query);
	        if($res){
	            $wheresql = "1 and FIND_IN_SET('{$res[id]}',doingtie)";
	        }else{
	            $wheresql = "1 and doingtie='-11111111111'";
	        }
	    }else{
	        $wheresql = "(doingtie <> '' and doingtie<>'0')";
	    }
	   	
		$ordersql = "dateline DESC";
		$theurl = "space.php?uid=$space[uid]&do=$do&view=tags&tags={$_GET[tags]}";
		$f_index = '';
	} else {
	
		if(empty($space['feedfriend'])) $_GET['view'] = 'me';
		
		if( $_GET['view'] == 'me') {
			$wheresql = "uid='$space[uid]'";
			$ordersql = "dateline DESC";
			$theurl = "space.php?uid=$space[uid]&do=$do&view=me";
			$f_index = '';
			
		} else {
			$feedfriends = $space['feedfriend'].",".$_SGLOBAL['supe_uid'];
			$wheresql = "uid IN ('0',{$feedfriends})";
			$ordersql = "dateline DESC";
			$theurl = "space.php?uid=$space[uid]&do=$do&view=we";
			$f_index = 'USE INDEX(dateline)';
			$_GET['view'] = 'we';
			//����ʾʱ��
			$_TPL['hidden_time'] = 1;
		}
	}
	
	//����
	$appid = empty($_GET['appid'])?0:intval($_GET['appid']);
	if($appid) {
		$wheresql .= " AND appid='$appid'";
	}
	$icon = empty($_GET['icon'])?'':trim($_GET['icon']);
	if($icon) {
		$wheresql .= " AND icon='$icon'";
	}
	$filter = empty($_GET['filter'])?'':trim($_GET['filter']);
	if($filter == 'site') {
		$wheresql .= " AND appid>0";
	} elseif($filter == 'myapp') {
		$wheresql .= " AND appid='0'";
	}
	
	$feed_list = $appfeed_list = $hiddenfeed_list = $filter_list = $hiddenfeed_num = $icon_num = array();
	$count = $filtercount = 0;
	$query = $_SGLOBAL['db']->query("SELECT f.*,((select count(*) from ".tname("docomment")." d where d.doid=f.id)) AS replynum FROM ".tname('feed')." f $f_index
		WHERE $wheresql 
		ORDER BY $ordersql
		LIMIT $start,$perpage");
	
	
	if($_GET['view'] == 'me' || $_GET['view'] == 'hot') {
		//���˶�̬
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			if(ckfriend($value['uid'], $value['friend'], $value['target_ids'])) {
				realname_set($value['uid'], $value['username']);
				$feed_list[] = $value;
			}
			$count++;
		}
	} else {
		//Ҫ�۵��Ķ�̬
		$hidden_icons = array();
		if($_SCONFIG['feedhiddenicon']) {
			$_SCONFIG['feedhiddenicon'] = str_replace(' ', '', $_SCONFIG['feedhiddenicon']);
			$hidden_icons = explode(',', $_SCONFIG['feedhiddenicon']);
		}
		$space['filter_icon'] = empty($space['privacy']['filter_icon'])?array():array_keys($space['privacy']['filter_icon']);
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			if(empty($feed_list[$value['hash_data']][$value['uid']])) {
				if(ckfriend($value['uid'], $value['friend'], $value['target_ids'])) {
					realname_set($value['uid'], $value['username']);
					if(ckicon_uid($value)) {
						$ismyapp = is_numeric($value['icon'])?1:0;
						if($_SCONFIG['my_showgift'] && $value['icon'] == $_SGLOBAL['gift_appid']) $ismyapp = 0;
						if((($ismyapp && in_array('myop', $hidden_icons)) || in_array($value['icon'], $hidden_icons)) && !empty($icon_num[$value['icon']])) {
							$hiddenfeed_num[$value['icon']]++;
							$hiddenfeed_list[$value['icon']][] = $value;
						} else {
							if($ismyapp) {
								$appfeed_list[$value['hash_data']][$value['uid']] = $value;
							} else {
								$feed_list[$value['hash_data']][$value['uid']] = $value;
							}
						}
						$icon_num[$value['icon']]++;
					} else {
						$filtercount++;
						$filter_list[] = $value;
					}
				}
			}
			$count++;
		}
	}
	
	$olfriendlist = $visitorlist = $task = $ols = $birthlist = $myapp = $hotlist = $guidelist = array();
	$oluids = array();
	$topiclist = array();
	$newspacelist = array();
	
	//你可能认识的人
	if(!$_COOKIE['friend_guide_close']){
	    $i = 0;
	    $mayukown = array();
	    $maxnum = 18;
	    $nouids = $space['friends'];
	    $nouids[] = $space['uid'];
	    if($space['feedfriend']) {
	    	$query = $_SGLOBAL['db']->query("SELECT fuid AS uid, fusername AS username FROM ".tname('friend')."
	    		WHERE uid IN (".$space['feedfriend'].") LIMIT 0,200");
	    	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
	    		if(!in_array($value['uid'], $nouids) && $value['username']) {
	    			realname_set($value['uid'], $value['username']);
	    			$mayukown[$value['uid']] = $value;
	    			$i++;
	    			if($i>=$maxnum) break;
	    		}
	    	}
	    }
	}
	
	
	if($space['self'] && empty($start)) {
	
		//����Ϣ
		$space['pmnum'] = $_SGLOBAL['member']['newpm'];
	
		//�ٱ�����
		if(checkperm('managereport')) {
			$space['reportnum'] = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('report')." WHERE new='1'"), 0);
		}
	
		//��˻
		if(checkperm('manageevent')) {
			$space['eventverifynum'] = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('event')." WHERE grade='0'"), 0);
		}
	
		//�ȴ�ʵ����֤
		if($_SCONFIG['realname'] && checkperm('managename')) {
			$space['namestatusnum'] = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('space')." WHERE namestatus='0' AND name!=''"), 0);
		}
		
	    //我的直播贴；
		include_once(S_ROOT.'./source/functionInit.php');
		$function = new functionInit();
		$uid = $space['uid'];
	    $myip = getonlineip(1);
	    $join = "m.uid='{$uid}'";
	    $query = $_SGLOBAL['db']->query("SELECT m.*,m.dateline as marktime,t.* FROM ".tname("mark"). " m,".tname("threads")." t WHERE {$join} AND m.tid=t.tid AND `type`='www' ORDER BY m.dateline DESC LIMIT 0,5");
	    while($value = $_SGLOBAL['db']->fetch_array($query)){
	        if($value['num'] <= 100){
	            $value['mypage'] = 1;
	        }else{
	            $value['mypage'] = ceil($value['num'] / 100);
	        }
	        $value['subject'] = $function->cutstr($value['subject'], 30,"....");
	        //$value['date']  = $date->diff(empty($value['marktime'])?time():$value['marktime']);
	        $mymarklist[] = $value;
	    }
	
	    
		//��ӭ�³�Ա
		if($_SCONFIG['newspacenum']>0) {
			$newspacelist = unserialize(data_get('newspacelist'));
			if(!is_array($newspacelist)) $newspacelist = array();
			foreach ($newspacelist as $value) {
				$oluids[] = $value['uid'];
				realname_set($value['uid'], $value['username'], $value['name'], $value['namestatus']);
			}
		}
	
		//���ÿ��б�
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('visitor')." WHERE uid='$space[uid]' ORDER BY dateline DESC LIMIT 0,3");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			realname_set($value['vuid'], $value['vusername']);
			$visitorlist[$value['vuid']] = $value;
			$oluids[] = $value['vuid'];
		}
	
		//�ÿ�����
		if($oluids) {
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('session')." WHERE uid IN (".simplode($oluids).")");
			while ($value = $_SGLOBAL['db']->fetch_array($query)) {
				if(!$value['magichidden']) {
					$ols[$value['uid']] = 1;
				} elseif ($visitorlist[$value['uid']]) {
					unset($visitorlist[$value['uid']]);
				}
			}
		}
	
		$oluids = array();
		$olfcount = 0;
		if($space['feedfriend']) {
			//���ߺ���
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('session')." WHERE uid IN ($space[feedfriend]) ORDER BY lastactivity DESC LIMIT 0,15");
			while ($value = $_SGLOBAL['db']->fetch_array($query)) {
				if(!$value['magichidden']) {
					realname_set($value['uid'], $value['username']);
					$olfriendlist[] = $value;
					$ols[$value['uid']] = 1;
					$oluids[$value['uid']] = $value['uid'];
					$olfcount++;
				}
			}
		}
		if($olfcount < 15) {
			//�ҵĺ���
			$query = $_SGLOBAL['db']->query("SELECT fuid AS uid, fusername AS username, num FROM ".tname('friend')." WHERE uid='$space[uid]' AND status='1' ORDER BY num DESC, dateline DESC LIMIT 0,30");
			while ($value = $_SGLOBAL['db']->fetch_array($query)) {
				if(empty($oluids[$value['uid']])) {
					realname_set($value['uid'], $value['username']);
					$olfriendlist[] = $value;
					$olfcount++;
					if($olfcount == 15) break;
				}
			}
		}
	
		//��ȡ����
		include_once(S_ROOT.'./source/function_space.php');
		$task = gettask();
	
		//��������
		if($space['feedfriend']) {
			list($s_month, $s_day) = explode('-', sgmdate('n-j', $_SGLOBAL['timestamp']-3600*24*3));//����3��
			list($n_month, $n_day) = explode('-', sgmdate('n-j', $_SGLOBAL['timestamp']));
			list($e_month, $e_day) = explode('-', sgmdate('n-j', $_SGLOBAL['timestamp']+3600*24*7));
			if($e_month == $s_month) {
				$wheresql = "sf.birthmonth='$s_month' AND sf.birthday>='$s_day' AND sf.birthday<='$e_day'";
			} else {
				$wheresql = "(sf.birthmonth='$s_month' AND sf.birthday>='$s_day') OR (sf.birthmonth='$e_month' AND sf.birthday<='$e_day' AND sf.birthday>'0')";
			}
			$query = $_SGLOBAL['db']->query("SELECT s.uid,s.username,s.name,s.namestatus,s.groupid,sf.birthyear,sf.birthmonth,sf.birthday
				FROM ".tname('spacefield')." sf
				LEFT JOIN ".tname('space')." s ON s.uid=sf.uid
				WHERE (sf.uid IN ($space[feedfriend])) AND ($wheresql)");
			while ($value = $_SGLOBAL['db']->fetch_array($query)) {
				realname_set($value['uid'], $value['username'], $value['name'], $value['namestatus']);
				$value['istoday'] = 0;
				if($value['birthmonth'] == $n_month && $value['birthday'] == $n_day) {
					$value['istoday'] = 1;
				}
				$key = sprintf("%02d", $value['birthmonth']).sprintf("%02d", $value['birthday']);
				$birthlist[$key][] = $value;
				ksort($birthlist);
			}
		}
	
		//���
		$space['star'] = getstar($space['experience']);
	
		//����
		$space['domainurl'] = space_domain($space);
	
		//�ȵ�
		if($_SCONFIG['feedhotnum'] > 0 && ($_GET['view'] == 'we' || $_GET['view'] == 'all')) {
			$hotlist_all = array();
			$hotstarttime = $_SGLOBAL['timestamp'] - $_SCONFIG['feedhotday']*3600*24;
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('feed')." USE INDEX(hot) WHERE dateline>='$hotstarttime' ORDER BY hot DESC LIMIT 0,10");
			while ($value = $_SGLOBAL['db']->fetch_array($query)) {
				if($value['hot']>0 && ckfriend($value['uid'], $value['friend'], $value['target_ids'])) {
					realname_set($value['uid'], $value['username']);
					if(empty($hotlist)) {
						$hotlist[$value['feedid']] = $value;
					} else {
						$hotlist_all[$value['feedid']] = $value;
					}
				}
			}
			$nexthotnum = $_SCONFIG['feedhotnum'] - 1;
			if($nexthotnum > 0) {
				if(count($hotlist_all)> $nexthotnum) {
					$hotlist_key = array_rand($hotlist_all, $nexthotnum);
					if($nexthotnum == 1) {
						$hotlist[$hotlist_key] = $hotlist_all[$hotlist_key];
					} else {
						foreach ($hotlist_key as $key) {
							$hotlist[$key] = $hotlist_all[$key];
						}
					}
				} else {
					$hotlist = $hotlist_all;
				}
			}
		}
		
		//����
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('topic')." ORDER BY lastpost DESC LIMIT 0,1");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$value['pic'] = $value['pic']?pic_get($value['pic'], $value['thumb'], $value['remote']):'';
			$topiclist[] = $value;
		}
	
	
		//��������
		$space['allnum'] = 0;
		foreach (array('notenum', 'addfriendnum', 'mtaginvitenum', 'eventinvitenum', 'myinvitenum', 'pokenum', 'reportnum', 'namestatusnum', 'eventverifynum') as $value) {
			$space['allnum'] = $space['allnum'] + $space[$value];
		}
	}
	
	//ʵ����
	realname_get();
	
	//feed�ϲ�
	$list = array();
	
	if($_GET['view'] == 'hot') {
		//�ȵ�
		foreach ($feed_list as $value) {
			$value = mkfeed($value);
			$list['today'][] = $value;
		}
	} elseif($_GET['view'] == 'me') {
		//����
		foreach ($feed_list as $value) {
			if($hotlist[$value['feedid']]) continue;
			$value = mkfeed($value);
			if($value['dateline']>=$_SGLOBAL['today']) {
				$list['today'][] = $value;
			} elseif ($value['dateline']>=$_SGLOBAL['today']-3600*24) {
				$list['yesterday'][] = $value;
			} else {
				$theday = sgmdate('Y-m-d', $value['dateline']);
				$list[$theday][] = $value;
			}
		}
	}else {
		//���ѡ�ȫվ
		foreach ($feed_list as $values) {
			$actors = array();
			$a_value = array();
			foreach ($values as $value) {
				if(empty($a_value)) {
					$a_value = $value;
				}
	            if($value[uid]){
	                $actors[] = "<a href=\"space.php?uid=$value[uid]\" class=\"bind_hover_card\" style=\"font-size: 13px;font-weight: bold;\" bm_user_id=\"$value[uid]\">".$_SN[$value['uid']]."</a>";
	            }else{
	                 $actors[] = cplang("none");
	            }
			}
			if($hotlist[$a_value['feedid']]) continue;
			$a_value = mkfeed($a_value, $actors);
			if($a_value['dateline']>=$_SGLOBAL['today']) {
				$list['today'][] = $a_value;
			} elseif ($a_value['dateline']>=$_SGLOBAL['today']-3600*24) {
				$list['yesterday'][] = $a_value;
			} else {
				$theday = sgmdate('Y-m-d', $a_value['dateline']);
				$list[$theday][] = $a_value;
			}
		}
		//Ӧ��
		foreach ($appfeed_list as $values) {
			$actors = array();
			$a_value = array();
			foreach ($values as $value) {
				if(empty($a_value)) {
					$a_value = $value;
				}
				$actors[] = "<a href=\"space.php?uid=$value[uid]\">".$_SN[$value['uid']]."</a>";
			}
			$a_value = mkfeed($a_value, $actors);
			$list['app'][] = $a_value;
		}
	}
	
	//��ø���ģ��
	$templates = $default_template = array();
	$tpl_dir = sreaddir(S_ROOT.'./template');
	foreach ($tpl_dir as $dir) {
		if(file_exists(S_ROOT.'./template/'.$dir.'/style.css')) {
			$tplicon = file_exists(S_ROOT.'./template/'.$dir.'/image/template.gif')?'template/'.$dir.'/image/template.gif':'image/tlpicon.gif';
			$tplvalue = array('name'=> $dir, 'icon'=>$tplicon);
			if($dir == $_SCONFIG['template']) {
				$default_template = $tplvalue;
			} else {
				$templates[$dir] = $tplvalue;
			}
		}
	}
}else{
		//查询用户KEY；
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("oauth")." WHERE uid='{$space[uid]}' AND type='douban'");
		$array = $_SGLOBAL['db']->fetch_array($query);
		include_once(S_ROOT."./apps/oauth/action/oauthConnect.php");
		$connect = new oauthConnect();
		$doubanlist = $connect->getminiblog("douban", $array["oid"], $array["token"], $array["token_secret"]);
		foreach ($doubanlist as $key => $value){
			$value['t'] = "douban";
			$value['dateline'] = date("Y-m-d H:i:s",strtotime($value['published']["\$t"]));
			$value['title_template'] = $value["content"]["\$t"];
			$list[] = $value;
		}
		print_r($list);
}

$_TPL['templates'] = $templates;
$_TPL['default_template'] = $default_template;

//��ǩ����
$my_actives = array(in_array($_GET['filter'], array('site','myapp'))?$_GET['filter']:'all' => ' class="active"');
$actives = array(in_array($_GET['view'], array('me','all','hot','tags',"douban"))?$_GET['view']:'we' => ' class="active"');

if(empty($cp_mode)) include_once template("space_feed");

//ɸѡ
function ckicon_uid($feed) {
	global $_SGLOBAL, $space, $_SCONFIG;

	if($space['filter_icon']) {
		$key = $feed['icon'].'|0';
		if(in_array($key, $space['filter_icon'])) {
			return false;
		} else {
			$key = $feed['icon'].'|'.$feed['uid'];
			if(in_array($key, $space['filter_icon'])) {
				return false;
			}
		}
	}
	return true;
}

//�Ƽ�����
function my_showgift() {
	global $_SGLOBAL, $space, $_SCONFIG;
	if($_SCONFIG['my_showgift'] && $_SGLOBAL['my_userapp'][$_SGLOBAL['gift_appid']]) {
		echo '<script language="javascript" type="text/javascript" src="http://gift.manyou-apps.com/recommend.js"></script>';
	}
}

?>