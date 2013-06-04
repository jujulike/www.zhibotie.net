<?php

/**
 * @author �ڿ�����
 * @copyright 2012
 */


$_SGLOBAL['is_sina_bind'] = false;
$_SGLOBAL['is_qq_bind'] = false;
$_SGLOBAL['is_renren_bind'] = false;
$_SGLOBAL['is_douban_bind'] = false;
$_SGLOBAL['is_ntes_bind'] = false;
$_SGLOBAL['is_souhu_bind'] = false;

$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("oauth")." WHERE uid = '{$_SGLOBAL['supe_uid']}'");
while($value = $_SGLOBAL['db']->fetch_array($query)){
    switch($value['type']){
        case "renren":
            $_SGLOBAL['is_renren_bind'] = true;
            break;
        case "qq":
            $_SGLOBAL['is_qq_bind'] = true;
            break;
        case "sina":
            $_SGLOBAL['is_sina_bind'] = true;
            break;
        case "douban":
            $_SGLOBAL['is_douban_bind'] = true;
            break;
        case "ntes":
            $_SGLOBAL['is_ntes_bind'] = true;
            break;
    }
}



?>