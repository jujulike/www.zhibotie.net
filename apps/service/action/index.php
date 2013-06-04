<?php

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$option = $_GET['option'];

if(empty($option)){
    include template("apps/{$m}/{$apps_config[tpl]}{$a}");
}elseif($option == "submit"){
    include_once("./source/function_bbcode.php");
    $emial = js_unescape($_GET["email"]);
    $subject = js_unescape($_GET['subject']);
    $author = js_unescape($_GET['author']);
    include_once("./source/class.seo.php");
    $seo = new SeoTools();
    //判断邮箱地址是否正确
    if($emial && !$seo->isEmail($emial)){
        echo ajax_return(0,"邮箱地址不正确");
        exit();    
    }elseif(strlen(trim($subject)) >= 500){
        echo ajax_return(0,"意见太多，通过邮箱来联系我们把: admin@zhibotie.net");
        exit();
    }
    $server = array();
    $server['author'] = mysql_real_escape_string($author);
    $server['message'] = mysql_real_escape_string($subject);
    $server['dateline'] = time();
    $server['isreply'] = 0;
    $server['reply_message'] = "";
    $server['email'] = mysql_real_escape_string($emial);
    $sid = inserttable("service",$server,1);
    if($sid){
        echo ajax_return(1,"：) 感谢你的意见，我们将随时倾听");
        exit();
    }else{
        echo ajax_return(0,"系统错误");
        exit();
    }
}