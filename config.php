<?php
/*
	[Ucenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: config.new.php 9293 2008-10-30 06:44:42Z liguode $
*/

//Ucenter Home���ò���
$_SC = array();
$_SC['dbhost']  		= 'localhost'; //��������ַ
$_SC['dbuser']  		= 'zhibotie'; //�û�
$_SC['dbpw'] 	 		= 'zhibotie+881203'; //����
$_SC['dbcharset'] 		= 'utf8'; //�ַ�
$_SC['pconnect'] 		= 0; //�Ƿ��������
$_SC['dbname']  		= 'www.zhibotie.net'; //��ݿ�
$_SC['tablepre'] 		= 'uchome_'; //����ǰ׺
$_SC['charset'] 		= 'utf-8'; //ҳ���ַ�


$_SC['gzipcompress'] 	= 1; //启用gzip

$_SC['cookiepre'] 		= 'uchome_'; //COOKIE前缀
$_SC['cookiedomain'] 	= '.zhibotie.net'; //COOKIE作用域
$_SC['cookiepath'] 		= '/'; //COOKIE作用路径

$_SC['attachdir']		= './attachment/'; //附件本地保存位置(服务器路径, 属性 777, 必须为 web 可访问到的目录, 相对目录务必以 "./" 开头, 末尾加 "/")
$_SC['attachurl']		= 'attachment/'; //附件本地URL地址(可为当前 URL 下的相对地址或 http:// 开头的绝对地址, 末尾加 "/")

$_SC['siteurl']			= ''; //站点的访问URL地址(http:// 开头的绝对地址, 末尾加 "/")，为空的话，系统会自动识别。

$_SC['tplrefresh']		= 0; //判断模板是否更新的效率等级，数值越大，效率越高; 设置为0则永久不判断

//Ucenter Home安全相关
$_SC['founder'] 		= '1,2656,2655'; //创始人 UID, 可以支持多个创始人，之间使用 “,” 分隔。部分管理功能只有创始人才可操作。
$_SC['allowedittpl']	= 0; //是否允许在线编辑模板。为了服务器安全，强烈建议关闭

//是否开启默认只看楼主？
$_SC['showauthor']      = 1; //默认为1， 1为开启 0为关闭
$_SC['getothercommont'] = 1; //是否开启获取第三人留言？ 1为开启 0为关闭 默认为1  

//应用的UCenter配置信息(可以到UCenter后台->应用管理->查看本应用->复制里面对应的配置信息进行替换)

define('UC_CONNECT', 'mysql');
define('UC_DBHOST', 'localhost');
define('UC_DBUSER', 'zhibotie');
define('UC_DBPW', 'zhibotie+881203');
define('UC_DBNAME', 'www.zhibotie.net');
define('UC_DBCHARSET', 'utf8');
define('UC_DBTABLEPRE', '`www.zhibotie.net`.uc_');
define('UC_DBCONNECT', '0');
define('UC_KEY', 'e5b9sE2BXkyH+BbXDCNb9s6IP9LkBhcf6S/HvcfC6g0M2OqZK6Qt4ewG58wf+HAOrjRMUrX2vTN3S3ZMgnLDrjqbwVAg0jWd6BZahfgIXuE4ZvCmeNLtfU8U2U+F');
define('UC_API', 'http://uc.zhibotie.net');
define('UC_CHARSET', 'utf-8');
define('UC_IP', '121.199.24.183');
define('UC_APPID', '4');
define('UC_PPP', '20');