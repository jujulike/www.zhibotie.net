<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: cp_doing.php 13245 2009-08-25 02:01:40Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

session_start();

$doid = empty($_GET['doid'])?0:intval($_GET['doid']);
$id = empty($_GET['id'])?0:intval($_GET['id']);
if(empty($_POST['refer'])) $_POST['refer'] = "space.php?do=doing&view=me";

/**
 * @param 直播提分享图片
 * @author 内裤叔叔
 * @time 2012..03.13
 */
$option = $_GET['option'];
if($option == "InsertImage"){
    include_once("source/function_class_image.php");
    if($_POST['source'] == 1){
            $upload_file_name = $_FILES['file']['name']; //获取上传名称
            if($upload_file_name){
            $upload__file_dir = "mood/".date("Ym")."/".date("d")."/"; //定义上传目录//.strtoupper(substr(md5($upload_file_name),0,2))."/"; //ָ���ϴ�Ŀ¼
            $file_type = end(explode('.',$upload_file_name)); //获取文件后缀名׺
            $upload_file_name = "{$upload__file_dir}{$_SGLOBAL['supe_uid']}_{$_SGLOBAL['timestamp']}".random(4).".".$file_type; //重定义文件名
            $upload_file_s_name = strtoupper(md5(date("ymdhis"))).'.'.$file_type; //重新组合
            $upload_allow_type = array("image/jpeg","image/jpg","image/png","image/gif","image/pjpeg","image/x-png");//允许上传的图片类型
            $upload_file_type = $_FILES['file']['type']; //获取上传文件类型
            $upload_file_size = $_FILES['file']['size']; //获取上传文件尺寸
            $upload_tamp_dir =  $_FILES['file']['tmp_name']; //获取上传的临时目录
            $upload_allow_size = 500000 * 6; //允许上传的最大尺寸
            if(!in_array($upload_file_type,$upload_allow_type)){
                echo json_encode(array("message"=>array("status"=>-1)));
                exit();
            }
            if($upload_file_size > $upload_allow_size){
                echo json_encode(array("message"=>array("status"=>0)));
                exit();
            }
            $image = new imageInit();
            $image->createFolder("./data/".$upload__file_dir);
            if(!move_uploaded_file($upload_tamp_dir,"./data/{$upload_file_name}")){ //移动文件
                echo json_encode(array("message"=>array("status"=>1)));
                exit();
            }else{
                //$new_file_name = $upload_file_name.".thump.".$file_type;
                //获取
                list($file_info['width'], $file_info['height'], $file_info['type']) =  @getimagesize("./data/{$upload_file_name}");
                //等比裁剪图片
                //FTP 上传
	            if($_SCONFIG['allowftp']) {
					include_once(S_ROOT.'./source/function_ftp.php');
					if(ftpupload("./data/{$upload_file_name}", $upload_file_name)) {
						$pic_remote = 1;
						$album_picflag = 2;
						$upload_file_name = $_SCONFIG['ftpurl'].$upload_file_name;
					} else {
						@unlink("./data/{$upload_file_name}");
						@unlink("./data/{$upload_file_name}.thumb.jpg");
						runlog('ftp', 'Ftp Upload ./data/'.$upload_file_name.' failed.');
						return cplang('ftp_upload_file_size');
					}
				}
                include_once(S_ROOT.'./source/function_image.php');
                $thumbpath = $upload_file_name."!small";//makethumb($upload_file_name);
                $file_upload = array();
                $file_upload['width'] = $file_info['width'];
                $file_upload['height'] = $file_info['height'];
                $file_upload['dateline'] = time();
                $file_upload['image_url'] = "";
                $file_upload['filesize'] = $upload_file_size;
                $file_upload['path_name'] = $upload_file_s_name;
                $file_upload['path'] = $upload_file_name;
                $file_upload['path_thump'] = $thumbpath;
                $file_upload['authorid'] = $_SGLOBAL['supe_uid'];
                $file_upload['author'] = $_SGLOBAL['supe_username'];
                $id = inserttable("fileimage",$file_upload,1); 
                if($id){
                    echo json_encode(array("message"=>array("status"=>2,"path"=>$upload_file_name,"thumbpath"=>$thumbpath,"name"=>$upload_file_s_name,"id"=>$id)));
                    exit();
                }else{
                    echo json_encode(array("message"=>array("status"=>1)));
                    exit();
                }
            }
            unset($image);
        }else{
            echo json_encode(array("message"=>array("status"=>-1)));
            exit();
        }
    }elseif($_POST['source'] == 2){
        $url = isset($_POST['url'])?trim($_POST['url']):"";
        if($url){
            $array = @get_headers($url);
            if(preg_match('/200/',$array[0]) || preg_match('/301/',$array[0])) {
                $image = new imageInit();
                
                $upload_file_size = $image->getFileSize($url);
                $upload_file_s_name = current(explode(".",end(explode("/",$url))));
                
                $path = "attachment/mood/".date("Ymd")."/";
                
                $string = $image->get_photo($url,"",$path);
                if(!$string){
                    echo json_encode(array("message"=>array("status"=>-1)));
                    exit();
                }
                
                include_once(S_ROOT.'./source/function_image.php');
                $thumbpath = makethumb($string);
                list($file_info['width'], $file_info['height'], $file_info['type']) =  @getimagesize($string);
                $file_upload = array();
                $file_upload['width'] = $file_info['width'];
                $file_upload['height'] = $file_info['height'];
                $file_upload['dateline'] = time();
                $file_upload['image_url'] = $url;
                $file_upload['filesize'] = $upload_file_size;
                $file_upload['path_name'] = $upload_file_s_name;
                $file_upload['path'] = $string;
                $file_upload['path_thump'] = $thumbpath;
                $file_upload['authorid'] = $_SGLOBAL['supe_uid'];
                $file_upload['author'] = $_SGLOBAL['supe_username'];
                $id = inserttable("fileimage",$file_upload,1); 
                if($id){
                echo json_encode(array("message"=>array("status"=>2,"path"=>$string,"thumbpath"=>$thumbpath,"name"=>$upload_file_s_name,"id"=>$id)));
                    exit();
                }else{
                    echo json_encode(array("message"=>array("status"=>1)));
                    exit();
                }
            }else{
                echo json_encode(array("message"=>array("status"=>3)));
                exit();
            }
        }else{
            echo json_encode(array("message"=>array("status"=>4)));
            exit();
        }
    }
    exit();
}elseif($option == "delete"){
    $id = mysql_real_escape_string($_GET['id']);
    $query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("fileimage")." WHERE id='{$id}'");
    $ret = $_SGLOBAL['db']->fetch_array($query);
    if($ret){
        del_file($ret['path']);
        del_file($ret['path_thump']);
        $_SGLOBAL['db']->query("DELETE FROM ".tname("fileimage")." WHERE id='{$id}'");
        echo ajax_return(1);
        exit();
    }else{
        echo ajax_return(0,"error");
        exit();
    }
}elseif($option == "editspacenote"){
    if($_SGLOBAL['supe_uid']){
        include_once(S_ROOT."source/function_bbcode.php");
        $update_value = html2bbcode($_POST['update_value']);
        updatetable("spacefield",array("spacenote"=>$update_value),array("uid"=>$_SGLOBAL['supe_uid']));
        //��ɶ�̬��
        $feedarr = array(
			'appid' => UC_APPID,
			'icon' => 'update_space',
			'uid' => $_SGLOBAL['supe_uid'],
			'username' => $_SGLOBAL['supe_username'],
			'dateline' => $_SGLOBAL['timestamp'],
			'title_template' => cplang('update_space'),
			'title_data' => saddslashes(serialize(sstripslashes(array('message'=>$update_value)))),
			'body_template' => '',
			'body_data' => '',
			'id' => 0,
			'idtype' => '',
            'image_1'=>"",
		);
		$feedarr['hash_template'] = md5($feedarr['title_template']."\t".$feedarr['body_template']);//ϲ��hash
		$feedarr['hash_data'] = md5($feedarr['title_template']."\t".$feedarr['title_data']."\t".$feedarr['body_template']."\t".$feedarr['body_data']);//�ϲ�hash
		inserttable('feed', $feedarr);
        echo $update_value;        
        //echo json_encode(array("message"=>$update_value));
        exit();
    }else{
        echo "error";
        exit();
    }
}

if(submitcheck('addsubmit')) {  
	$add_doing = 1;
	if(empty($_POST['spacenote'])) {
		if(!checkperm('allowdoing')) {
			ckspacelog();
			showmessage('no_privilege');
		}
		
		//ʵ����֤
		ckrealname('doing');
		
		//��Ƶ��֤
		ckvideophoto('doing');
		
		//���û���ϰ
		cknewuser();
	
		//��֤��
		if(checkperm('seccode') && !ckseccode($_POST['seccode'])) {
			showmessage('incorrect_code');
		}
	
		//�ж��Ƿ����̫��
		$waittime = interval_check('post');
		if($waittime > 0) {
			showmessage('operating_too_fast', '', 1, array($waittime));
		}
	} else {
		if(!checkperm('allowdoing')) {
			$add_doing = 0;
		}

		//ʵ��
		if(!ckrealname('doing', 1)) {
			$add_doing = 0;
		}
		//��Ƶ
		if(!ckvideophoto('doing', array(), 1)) {
			$add_doing = 0;
		}
		//���û�
		if(!cknewuser(1)) {
			$add_doing = 0;
		}
		$waittime = interval_check('post');
		if($waittime > 0) {
			$add_doing = 0;
		}
	}
	
	//��ȡ����
	$mood = 0;
	preg_match("/\[em\:(\d+)\:\]/s", $_POST['message'], $ms);
	$mood = empty($ms[1])?0:intval($ms[1]);

	$message = getstr($_POST['message'], 0, 1, 1, 1);
	$message_content = $message;
    //�滻#(.*?)#
    $message = preg_replace_callback("/#(.*?)#/is","get_huati",$message);
	//�滻����
	$message = preg_replace("/\[em:(\d+):]/is", "<img src=\"image/face/\\1.gif\" class=\"face\">", $message);
	$message = preg_replace("/\<br.*?\>/is", ' ', $message);
	
	/**
	 * 内裤叔叔添加此项用于发布动态的时候将长网址转换为短网址
	 */
	$message = preg_replace_callback("/(http[s]?:\/\/w{0,3}[^\s]*)/s","callback_url",$message);	
	
	//提到某人
	//$message = preg_replace_callback('/@([\w\-]+|[^x00-xff\s]+)\s/is', "uname", $message);
	$message = preg_replace_callback("/@(.+?)([\s|:]|$)/is", "uname", $message);
	
	
	if(strlen($message) < 1) {
		showmessage('should_write_that');
	}
    $attachment = ($_POST['TweetAttachment'])?mysql_real_escape_string($_POST['TweetAttachment']):0;
    $taobaoid   = isset($_POST['TweetTaobaoShare'])?$_POST['TweetTaobaoShare']:0;
    
	if($add_doing) {
		$setarr = array(
			'uid' => $_SGLOBAL['supe_uid'],
			'username' => $_SGLOBAL['supe_username'],
			'dateline' => $_SGLOBAL['timestamp'],
			'message' => $message,
			'mood' => $mood,
			'ip' => getonlineip(),
            'attachment' => $attachment,
			'taoid'=>$taobaoid,
            'doingtie'=>$_SGLOBAL['do_huati'],
		);
        
		//���
		$newdoid = inserttable('doing', $setarr, 1);
        
	}
	
	atme($newdoid,$message_content);
	
	//���¿ռ�note
	$setarr = array('note'=>$message);
	$credit = $experience = 0;
	if(!empty($_POST['spacenote'])) {
		$reward = getreward('updatemood', 0);
		$setarr['spacenote'] = $message;
	} else {
		$reward = getreward('doing', 0);
	}
	updatetable('spacefield', $setarr, array('uid'=>$_SGLOBAL['supe_uid']));
	
	if($reward['credit']) {
		$credit = $reward['credit'];
	}
	if($reward['experience']) {
		$experience = $reward['experience'];
	}
	$setarr = array(
		'mood' => "mood='$mood'",
		'updatetime' => "updatetime='$_SGLOBAL[timestamp]'",
		'credit' => "credit=credit+$credit",
		'experience' => "experience=experience+$experience",
		'lastpost' => "lastpost='$_SGLOBAL[timestamp]'"
	);
	if($add_doing) {
		if(empty($space['doingnum'])) {//��һ��
			$doingnum = getcount('doing', array('uid'=>$space['uid']));
			$setarr['doingnum'] = "doingnum='$doingnum'";
		} else {
			$setarr['doingnum'] = "doingnum=doingnum+1";
		}
	}
	$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET ".implode(',', $setarr)." WHERE uid='$_SGLOBAL[supe_uid]'");
	
    //��ȡ����
    if($attachment){
        $query = $_SGLOBAL['db']->query("SELECT  id,path,path_thump from ".tname("fileimage")." WHERE id='{$attachment}'");
        $attach = $_SGLOBAL['db']->fetch_array($query);
        if($attach){
            $path_thump = $attach['path_thump'];
        }
    }
    $insterarray = array('message'=>$message);
    if($taobaoid){
    	$query = $_SGLOBAL['db']->query("SELECT localpic,subject,taokeurl,price FROM ".tname("taobao")." WHERE id='{$taobaoid}'");
   		$attach = $_SGLOBAL['db']->fetch_array($query);
        if($attach){
            $path_thump = str_replace("!big", "!middle.160", $attach['localpic']);
        }
        $insterarray['subject'] = $attach['subject'];
        $insterarray['taokeurl'] = $attach['taokeurl'];
        $insterarray['price'] = $attach['price'];
    }
    $idtype = $taobaoid?"taoid":"doid";
    
	//�¼�feed
	if($add_doing && ckprivacy('doing', 1)) {
		$feedarr = array(
			'appid' => UC_APPID,
			'icon' => $taobaoid?'taobao':'doing',
			'uid' => $_SGLOBAL['supe_uid'],
			'username' => $_SGLOBAL['supe_username'],
			'dateline' => $_SGLOBAL['timestamp'],
			'title_template' => cplang('feed_doing_title'),
			'title_data' => saddslashes(serialize(sstripslashes($insterarray))),
			'body_template' => '',
			'body_data' => '',
			'id' => $newdoid,
			'idtype' => $idtype,
            'image_1'=>$path_thump,
			'image_1_link' => $insterarray['taokeurl'],
            'doingtie'=>$_SGLOBAL['do_huati'],
		);
		$feedarr['hash_template'] = md5($feedarr['title_template']."\t".$feedarr['body_template']);//ϲ��hash
		$feedarr['hash_data'] = md5($feedarr['title_template']."\t".$feedarr['title_data']."\t".$feedarr['body_template']."\t".$feedarr['body_data']);//�ϲ�hash
		inserttable('feed', $feedarr);
	}

	//ͳ��
	updatestat('doing');
	
    $message_sina = join("",subString_UTF8($message_content,0,140));
	$baidu_url = short_url_163("http://www.zhibotie.net/space-doing-doid-{$newdoid}.html");
    //$baidu_url = sina_short_url("925507530","http://www.zhibotie.net/space-doing-doid-{$newdoid}.html");
    $message_douban = join("",subString_UTF8($message_content,0,125)) . "  from: http://{$baidu_url['url']}";

    if($_POST['douban_send']){
        $query = $_SGLOBAL['db']->query("SELECT * from ".tname("oauth") ." WHERE uid='{$_SGLOBAL['supe_uid']}' AND `type`='douban'");
        $douban_n = $_SGLOBAL['db']->fetch_array($query);
        if($douban_n){
            include_once(S_ROOT."./apps/oauth/action/oauthConnect.php");
            $newConnect = new oauthConnect();
            $return = $newConnect->add_new_weibo('douban',$message_douban,$douban_n['token'],$douban_n['token_secret']);
        } 
     }
     if($_POST['sina_send']){
        $query = $_SGLOBAL['db']->query("SELECT * from ".tname("oauth") ." WHERE uid='{$_SGLOBAL['supe_uid']}' AND `type`='sina'");
        $sina_n = $_SGLOBAL['db']->fetch_array($query);
        if($sina_n){
            include_once(S_ROOT."./apps/oauth/inc/sina.class.php");
            $sina_class = new sinaClass();
            $message_sina = shtmlspecialchars($message_sina);
            if(!$path_thump){
            	echo $message_sina;
            	$sina_class->update($sina_n['token'], $message_sina);
            }else{
            	$path = str_replace("!small","", $path_thump);
                $sina_class->update_img($sina_n['token'], $message_sina, $path);
            }
        }
     }
	setcookie("last_tweet_text","");
	if($_POST['index_feed']){
		exit(ajax_return(1,""));
	}
	else{
		showmessage('do_success', $_POST['refer'], 0);
	}
    $_SGLOBAL['do_huati'] = 0;

} elseif (submitcheck('commentsubmit')) {
	
	if(!checkperm('allowdoing')) {
		ckspacelog();
		showmessage('no_privilege');
	}
	
	//ʵ����֤
	ckrealname('doing');
	
	//���û���ϰ
	cknewuser();
	
	//�ж��Ƿ����̫��
	$waittime = interval_check('post');
	if($waittime > 0) {
		showmessage('operating_too_fast', '', 1, array($waittime));
	}
	
	//获取是否是转发
	$topicReplyType = $_POST['topicReplyType'];
	
	$message = getstr($_POST['message'], 200, 1, 1, 1);
	//�滻����
	$message = preg_replace("/\[em:(\d+):]/is", "<img src=\"image/face/\\1.gif\" class=\"face\">", $message);
	$message = preg_replace("/\<br.*?\>/is", ' ', $message);
	if(strlen($message) < 1) {
		showmessage('should_write_that');
	}
	
	$message = preg_replace_callback("/@(.+?)([\s|:]|$)/is", "uname", $message);	
	
	$updo = array();
	if($id) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('docomment')." WHERE id='$id'");
		$updo = $_SGLOBAL['db']->fetch_array($query);
	}
	if(empty($updo) && $doid) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('doing')." WHERE doid='$doid'");
		$updo = $_SGLOBAL['db']->fetch_array($query);
	}
	if(empty($updo)) {
		showmessage('docomment_error');
	} else {
		//����
		if(isblacklist($updo['uid'])) {
			showmessage('is_blacklist');
		}
	}
	
	//处理转发
	if($topicReplyType && $topicReplyType == 'bloh'){
		//添加doing表
		if($id){
			$query = $_SGLOBAL['db']->query("SELECT d.*,f.* FROM ".tname('doing')." d LEFT JOIN ".tname("fileimage")." f ON d.attachment=f.id WHERE d.doid='{$updo['doid']}'");
			$do = $_SGLOBAL['db']->fetch_array($query);
			if($do['attachment']){
				$s_message = "<br><a href=\"{$do['path']}\" class=\"miniImg artZoom\" rel=\"{$do['path']}\"><img src=\"{$do['path_thump']}\" class=\"summaryimg\"></a>";
			}
			$add_message = "{$message}//<a href=\"space.php?uid={$updo['uid']}\" class=\"bind_hover_card\" bm_user_id=\"{$updo['uid']}\">{$updo['username']}</a>:{$updo['message']}<div class=\"quote\"><span class=\"q\"><a href=\"space.php?uid={$do['uid']}\" class=\"bind_hover_card\" bm_user_id=\"{$do['uid']}\">@{$do['username']}</a>:{$do['message']}{$s_message}</span></div>";
			$topicReplyType_tpl = cplang('both_doing');
			$topicReplyType_data = saddslashes(serialize(sstripslashes(array('message'=>$message,"source_uid"=>$updo['uid'],"source_author"=>$updo['username'],"source_message"=>$updo['message'],
									"start_message"=>"<a href=\"space.php?uid={$do['uid']}\" class=\"bind_hover_card\" bm_user_id=\"{$do['uid']}\">@{$do['username']}</a>:{$do['message']}{$s_message}"
									))));
		}else{
			$add_message = "{$message}//<a href=\"space.php?uid={$updo['uid']}\" class=\"bind_hover_card\" bm_user_id=\"{$updo['uid']}\">{$updo['username']}</a>:{$updo['message']}";
			$topicReplyType_tpl = cplang('both_doing_base');
			$topicReplyType_data = saddslashes(serialize(sstripslashes(array('message'=>$message,"source_uid"=>$updo['uid'],"source_author"=>$updo['username'],"source_message"=>$updo['message']))));
		}
		$setarr = array(
			'uid' => $_SGLOBAL['supe_uid'],
			'username' => $_SGLOBAL['supe_username'],
			'dateline' => $_SGLOBAL['timestamp'],
			'message' => $add_message,
			'mood' => $mood,
			'ip' => getonlineip(),
		);
        
		//添加
		$newdoid = inserttable('doing', $setarr, 1);
		//添加@ME 通知
		atme($newdoid,$message);
		
		//处理动态
		if($newdoid && ckprivacy('doing', 1)) {
			$feedarr = array(
				'appid' => UC_APPID,
				'icon' => 'doing',
				'uid' => $_SGLOBAL['supe_uid'],
				'username' => $_SGLOBAL['supe_username'],
				'dateline' => $_SGLOBAL['timestamp'],
				'title_template' => $topicReplyType_tpl,
				'title_data' => $topicReplyType_data,
				'body_template' => '',
				'body_data' => '',
				'id' => $newdoid,
				'idtype' => 'doid',
			);
			$feedarr['hash_template'] = md5($feedarr['title_template']."\t".$feedarr['body_template']);//ϲ��hash
			$feedarr['hash_data'] = md5($feedarr['title_template']."\t".$feedarr['title_data']."\t".$feedarr['body_template']."\t".$feedarr['body_data']);//�ϲ�hash
			inserttable('feed', $feedarr);
		}
		
		//更新spacefild note 
		$setarr = array('note'=>$add_message);
		updatetable('spacefield', $setarr, array('uid'=>$_SGLOBAL['supe_uid']));
	}

	
	$updo['id'] = intval($updo['id']);
	$updo['grade'] = intval($updo['grade']);
	

	$setarr = array(
		'doid' => $updo['doid'],
		'upid' => $updo['id'],
		'uid' => $_SGLOBAL['supe_uid'],
		'username' => $_SGLOBAL['supe_username'],
		'dateline' => $_SGLOBAL['timestamp'],
		'message' => $message,
		'ip' => getonlineip(),
		'grade' => $updo['grade']+1
	);
	
	//���㼶
	if($updo['grade'] >= 3) {
		$setarr['upid'] = $updo['upid'];//��ĸһ������
	}

	$newid = inserttable('docomment', $setarr, 1);
	
	//���»ظ���
	$_SGLOBAL['db']->query("UPDATE ".tname('doing')." SET replynum=replynum+1 WHERE doid='$updo[doid]'");
	
	//֪ͨ
	if($updo['uid'] != $_SGLOBAL['supe_uid']) {
		if($topicReplyType){
			$note = cplang('note_doing_reply_both', array("space.php?do=doing&doid=$updo[doid]&highlight=$newid"));
		}else{
			$note = cplang('note_doing_reply', array("space.php?do=doing&doid=$updo[doid]&highlight=$newid"));
		}
			
		notification_add($updo['uid'], 'doing', $note);
		//������
		getreward('comment',1, 0, 'doing'.$updo['doid']);
	}
	
	//ͳ��
	updatestat('docomment');
	
	showmessage('do_success', $_POST['refer'], 0);

}

//ɾ��
if($_GET['op'] == 'delete') {
	
	if(submitcheck('deletesubmit')) {
		if($id) {
			$allowmanage = checkperm('managedoing');
			$query = $_SGLOBAL['db']->query("SELECT dc.*, d.uid as duid FROM ".tname('docomment')." dc, ".tname('doing')." d WHERE dc.id='$id' AND dc.doid=d.doid");
			if($value = $_SGLOBAL['db']->fetch_array($query)) {
				if($allowmanage || $value['uid'] == $_SGLOBAL['supe_uid'] || $value['duid'] == $_SGLOBAL['supe_uid'] ) {
					//��������
					updatetable('docomment', array('uid'=>0, 'username'=>'', 'message'=>''), array('id'=>$id));
					if($value['uid'] != $_SGLOBAL['supe_uid'] && $value['duid'] != $_SGLOBAL['supe_uid']) {
						//�۳���
						getreward('delcomment', 1, $value['uid']);
					}
				}
			}
		} else {
			include_once(S_ROOT.'./source/function_delete.php');
			deletedoings(array($doid));
		}
		
		showmessage('do_success', $_POST['refer'], 0);
	}
	
} elseif ($_GET['op'] == 'getcomment') {
	//��ȡ���� 
	/*
    if($_SGLOBAL['supe_uid']){
        $query = $_SGLOBAL['db']->query("SELECT * from ".tname("oauth"). " WHERE uid='{$_SGLOBAL['supe_uid']}'");
        $user = $_SGLOBAL['db']->fetch_array($query);
        if($user && $user['token'] && $user['token_secret']){
            $query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("oauth_doing")." WHERE doid='{$doid}'");
            $rest = $_SGLOBAL['db']->fetch_array($query);
            if($rest && $rest['mid']){
                include_once(S_ROOT."./apps/oauth/action/oauthConnect.php");
                $newConnect = new oauthConnect();
                $array = $newConnect->get_comment("douban",$rest['mid'],$user['token'],$user['token_secret']);
                echo $array;
                exit();
            }
        }
    }
    */
        
	include_once(S_ROOT.'./source/class_tree.php');
	$tree = new tree();
	
	$list = array();
	$highlight = 0;
	$count = 0;
	
	if(empty($_GET['close'])) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('docomment')." WHERE doid='$doid' ORDER BY dateline");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			realname_set($value['uid'], $value['username']);
			$tree->setNode($value['id'], $value['upid'], $value);
			$count++;
			if($value['authorid'] = $space['uid']) $highlight = $value['id'];
		}
	}
	
	$commonts = $count;
	if($count) {
		$values = $tree->getChilds();
		foreach ($values as $key => $vid) {
			$one = $tree->getValue($vid);
			$one['layer'] = $tree->getLayer($vid) * 2;
			$one['style'] = "padding-left:{$one['layer']}em;";
			if($one['id'] == $highlight && $one['uid'] == $space['uid']) {
				$one['style'] .= 'color:red;font-weight:bold;';
			}
			$list[] = $one;
		}
	}
	
	realname_get();
	
}

include template('cp_doing');

function get_huati($matches){
   global $_SGLOBAL;
   if($matches){
        $string = "<a href=\"space.php?do=home&view=tags&tags=".urlencode(trim($matches[1]))."\">".trim($matches[0])."</a>";
        $query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("doingtie")." where `hash`='".md5(trim($matches[1]))."'");
        $rest = $_SGLOBAL['db']->fetch_array($query);
        if(!$rest){
            $doingtie = array();
            $doingtie['name'] = mysql_real_escape_string($matches[1]);
            $doingtie['dateline'] = time();
            $doingtie['hash'] = md5($doingtie['name']);
            $doingtie['count'] = 1;
            $id = inserttable("doingtie",$doingtie,1);
            if($_SGLOBAL['do_huati'] != 0 && $_SGLOBAL['do_huati']){
                $_SGLOBAL['do_huati'] = $_SGLOBAL['do_huati'] ."," .$id;
                //setcookie("dotingtie",$_COOKIE['dotingtie'].",".$id);
            }else{
                $_SGLOBAL['do_huati'] = $id;
            }
        }else{
            $_SGLOBAL['db']->query("update ".tname("doingtie")." set `count`=`count`+1  where `hash`='".md5(trim($matches[1]))."'");
            if(!strexists($_SGLOBAL['do_huati'],$rest['id'])){
                $_SGLOBAL['do_huati'] = $_SGLOBAL['do_huati'] ."," .$rest['id'];
            }
        }
   }
   return $string;
}

	function uname($matches) {
	    $uname = $matches [1];
	    $uid = getuid($uname);
	    return "<a href=\"space.php?uid={$uid}\" class=\"bind_hover_card\" bm_user_id=\"{$uid}\">@{$uname}</a> ";
	}

	/**
	 * @param 提到我
	 * @param $doid int;
	 * 		  $data string;
	 * @return array();
	 */
	function atme($doid,$data){
		global $_SGLOBAL;
		preg_match_all("/@(.+?)([\s|:]|$)/is", $data, $matches);
		if($matches && $matches[1]){
			$unames = array_unique($matches[1]);
			//通过username 批量查找到uid
			foreach($unames as $k => $v){
				$unames[$k] = "'{$v}'";
			}
			$data = implode(",", $unames);
			$sql = "SELECT uid,username FROM ".tname("space")." WHERE username IN ({$data})";
			$query = $_SGLOBAL['db']->query($sql);
			while($value = $_SGLOBAL['db']->fetch_array($query)){
				if($value['uid'] != $_SGLOBAL['supe_uid']){
					notifyToAtme($value['uid'],$doid);
				}
			}
		}
	}
	
	
	/**
	 * @param @提到我添加通知
	 * @param $uid int;
	 * 		  $doid int;
	 */
	function notifyToAtme($uid ,$doid){
		$note = cplang("atme",array("space.php?do=doing&doid=$doid&highlight=$doid"));
		notification_add($uid, 'atme', $note);
	}
	
	function callback_url($matches){
		if(is_array($matches) && $matches[1]){
			$uri = short_url_163($matches[1]);
			return "<a href=\"http://{$uri[url]}\" target=\"_blank\">http://{$uri[url]}</a>";
		}else return false;
	}

?>