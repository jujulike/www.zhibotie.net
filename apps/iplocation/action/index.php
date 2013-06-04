<?php
/**
	将IP转换为真实地址
 */

$ip = isset($_GET['ip'])?trim($_GET['ip']):"";
include("apps/{$m}/class/location.class.php");
$iplocation = new IpLocation("apps/{$m}/QQWry.dat",false);
$return = $iplocation->getlocation($ip);
if(!$return){
	echo ajax_return(0,"ip error");
	exit();
}
echo ajax_return(1,"ok",$return);
exit();