<?php
/**
 *@param ÄÚ¿ãÊåÊå Ö±²¥Ìù 
 *@Id:   space_zhibotie.php
 *@version 0.1
 */
if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

//·ÖÒ³
$perpage = 20;
$perpage = mob_perpage($perpage);

$page = empty($_GET['page'])?0:intval($_GET['page']);
if($page<1) $page=1;
$start = ($page-1)*$perpage;

include_once template("space_zhibotie");