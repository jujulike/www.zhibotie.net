<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');   
$filePath = $_POST['filePath'];
$filename = $_POST['filename'];
$src = 'upload/'.$filename;
$imageSize = getimagesize($src);
if($imageSize[0]>300 || $imageSize[1]>300)
{
	$jpeg_quality = 90;
	if($imageSize[0] > $imageSize[1])
	{
		$targ_w = 300;
		$scale = $targ_w/$imageSize[0];
		$targ_h = $imageSize[1]*$scale;
	}
	else
	{
		$targ_h = 300;
		$scale = $targ_h/$imageSize[1];
		$targ_w = $imageSize[0]*$scale;
	}

	$img_r = imagecreatefromjpeg($src);
	$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
	imagecopyresampled($dst_r,$img_r,0,0,0,0,
			$targ_w,$targ_h,$imageSize[0],$imageSize[1]);
	imagejpeg($dst_r,$src,$jpeg_quality);
}
?>