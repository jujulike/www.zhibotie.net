<?php
include_once('./common.php');
include_once(S_ROOT.'./source/function_sendmail.php');

var_dump(sendmail($_GET['to'], "admin@zhibotie.net", "admin@zhibotie.net"));