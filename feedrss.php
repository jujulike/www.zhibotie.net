<?php
    header("Content-type:text/html;charset=utf-8");
    
    include_once('./common.php');
    
    include(S_ROOT."./source/class.rss.php");
    
    include_once(S_ROOT.'./source/function_bbcode.php');
    
    $rss = new RSS("直播贴 发现你身边的故事","http://www.zhibotie.net/","直播贴是基于豆瓣、天涯社区而成立的提供各种在线直播只看楼主，豆瓣、天涯数据帖子备份最好去处。www.zhibotie.net");
    $query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("threads"). " ORDER BY getdate DESC LIMIT 0,100");
    $value = $_SGLOBAL['db']->fetch_array($query);
    while($value = $_SGLOBAL['db']->fetch_array($query)){
        
        $value['message'] = preg_replace(array("/\[url\](.*?)\[\/url\]/is","/\[img\](.*?)\[\/img\]/is"), "", trim($value['message']));
        $value['message'] = bbcode($value['message'],1);
        $rss->AddItem("{$value['subject']}","http://www.zhibotie.net/live-view-tid-{$value['tid']}-only-author.html","{$value['message']}",Date('Y-m-d H:i:s', $value['getdate']));
    }
    $rss->Display();
 
?>
