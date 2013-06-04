<?php 

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$uid = isset($_GET['uid'])?$_GET['uid']:"";
$author = isset($_GET['author'])?$_GET['author']:"";
$action = isset($_GET['action'])?$_GET['action']:"";

if($action == "getfrineds" ){
    $query   = $_SGLOBAL['db']->query("SELECT note as relationship,uid,fuid,fusername as name FROM ".tname("friend")." WHERE uid='$uid'");
    $friends = $_SGLOBAL['db']->fetch_array($query);
    
    $li = array();
    $li['name'] = $space['username'];
    $li['friend'] = $friends;
    
    echo json_encode($li);
    exit();
}else{
   include_once template("space_frined_relation"); 
}

