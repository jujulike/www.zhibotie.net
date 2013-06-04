<?php

$option = isset($_GET['option'])?$_GET['option']:"login";

switch ($option){
	
	case "login":
		include template("apps/{$m}/{$apps_config[tpl]}login");
		break;
	
}


