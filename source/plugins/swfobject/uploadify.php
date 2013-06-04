<?php
if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
	$targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];
	move_uploaded_file($tempFile,iconv("UTF-8","gb2312", $targetFile));
	echo str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
}
?>