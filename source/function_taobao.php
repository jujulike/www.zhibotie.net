<?php

function taobao_upyun( $filename , $from = "")
{
	global $_SGLOBAL, $_SC ,$_SCONFIG;
	$path = "taobao/".date("Ym")."/".date("d");
	$fileext = strtolower(trim(substr(strrchr($filename, '.'), 1)));
	$filepath = "{$_SGLOBAL['supe_uid']}_{$_SGLOBAL['timestamp']}".random(4).".$fileext";
	$new_name = "{$path}/{$filepath}";
	include( S_ROOT . 'source/upyun.class.php' );
	ob_start();
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $filename );
	
	if( $from )
	{
		curl_setopt( $ch, CURLOPT_REFERER, $from );
	}
	
	curl_exec( $ch );
	$img = ob_get_contents();
	curl_close( $ch );
	ob_clean();
	$upyun = new UpYun( 'www-zhibotie-net', 'zhibotie', 'xiaomin19881203' );//创建目录
	$upyun -> mkDir( "/{$path}/", true );//写入文件
	if( $upyun -> writeFile( "/{$new_name}", $img ) )
	{
		return $_SCONFIG['ftpurl'].$new_name;
	}
	else
	{
		return false;
	}
}


/**
 * 
 * 短语分词API 
 * @param string $string
 * @return Array;
 */
function Scws_array($string){
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_URL, "http://www.ftphp.com/scws/api.php");
	curl_setopt($ch, CURLOPT_POSTFIELDS, "data={$string}&respond=json");
	
	ob_start();
	curl_exec($ch);
	$query = ob_get_contents();
	curl_close($ch);
	ob_clean();
	$query = json_decode($query,true);
	return $query;
	
}

function delete_upyun($filename){
	include( S_ROOT . 'source/upyun.class.php' );
	$upyun = new UpYun( 'www-zhibotie-net', 'zhibotie', 'xiaomin19881203' );//创建目录
	return $upyun->deleteFile($filename);
}