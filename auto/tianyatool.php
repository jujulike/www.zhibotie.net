<?php

/**
 * SQL 
 * 
    CREATE TABLE `uchome_auto` (
      `aid` mediumint(8) NOT NULL,
      `uri` varchar(100) NOT NULL,
      `type` varchar(20) NOT NULL DEFAULT 'tianya',
      `islock` tinyint(1) NOT NULL DEFAULT '0',
      `dateline` int(10) NOT NULL,
      PRIMARY KEY (`aid`),
      KEY `type` (`type`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

 */
header("Content-type:text/html;charset=utf-8");
set_time_limit(0);
include_once "../common.php";
$url = "http://www.tianyatool.com/static/";
if($_GET['asdfghjkl'] == "123456789QWERTYUUIIO"){
    $data = fopen_uri_t($url);
    $content = mb_convert_encoding($data['content'],"utf-8","GB2312");
    preg_match_all("/<a(.*?)HREF\=\'http\:\/\/bbs\.tianya\.cn\/(.*?)\.shtml\'/is", $content,$tianyauri);
    foreach ($tianyauri[2] as $key => $value){
        $list["uri"] = "http://bbs.tianya.cn/{$value}.shtml";
        $list["type"] = "tianya";
        $list["islock"] = 0;
        $list['dateline'] = time();
        $data = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query("SELECT * FROM ".tname("auto")." WHERE uri='{$list['uri']}' LIMIT 1"));
        if(!$data){
            $aid = inserttable("auto", $list , 1);
        }
    }
    if($aid){
        file_put_contents($lockname, time() + 3600);
    }
    exit("Ok ! time: ".date("Ymd H:i:s",time()));
}else{
    exit("No push! time: ".date("Ymd H:i:s",time()));
}

function fopen_uri_t($uri){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data['content'] = curl_exec($ch);
    $data['header'] = curl_getinfo($ch);
    curl_close($ch);
    return $data;
}



function get_File_time($filename){
    $filename = $filename ? $filename : S_ROOT."./data/tianya_auto.lock";
    $dateline = @file_get_contents($filename);
    return $dateline;
}