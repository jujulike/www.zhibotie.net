<?php
/**
 * 用户获取被盗链图片 
 */
header('Content-type:text/html;charset=utf-8');
include_once './common.php';
$_GET = saddslashes($_GET);
$allow_path = array('tianya', 'baidu', 'douban', 'doubanhead', 'baiduhead', 'tianyahead');
$allow_host = array('zhibotie.net');
$from = isset($_GET['from']) ? strtolower($_GET['from']) : '';
$uri = isset($_GET['uri']) ? urldecode($_GET['uri']) : '';
$remote = isset($_GET['remote']) ? trim($_GET['remote']) : 0;
$path = isset($_GET['path']) ? strtolower(trim($_GET['path'])) : 'tianya';
$size = isset($_GET['size']) ? $_GET['size'] : '';
$size = $size ? "!{$size}" : '';
// 判断来路
// if(strstr($_SERVER['HTTP_REFERER'], "www.zhibotie.net")) exit("host error!!");
// 判断$path
if (!$path || !in_array($path, $allow_path)) {
    die('path error!!');
}
if ($path == 'baiduhead' || $path == 'tianyahead') {
    $fileext = 'jpg';
}
if ($from) {
    $fromlist = explode('/', $uri);
    $fromlist_new = explode('.', $fromlist[2]);
    $from_new = "http://*.{$fromlist_new[1]}.{$fromlist_new[2]}";
    $from = $from == $from_new ? $from : $from_new;
}


if ($uri) {
    if (!$remote) {
        include S_ROOT . './source/function_class_image.php';
        $image = new imageInit();
        header('Location:' . $image->get_photo($uri, '', "attachment/{$path}/", $from));
    } else {
        // 加入 CACHE
        $path = "cache/{$path}";
       
        // 同样要判断图片是否存在
        if ($_SCONFIG['allowftp']) {
            $urlmd5 = strtoupper(md5($uri));
            
            $fileext = $fileext ? $fileext : strtolower(trim(substr(strrchr($uri, '.'), 1)));
            $newC = (substr($urlmd5, 0, 2) . '/') . substr($urlmd5, 2, 2);
            $path = "{$path}/{$newC}";
            $filename = "{$_SCONFIG['ftpurl']}{$path}/{$urlmd5}.{$fileext}";
            $new_name = "{$path}/{$urlmd5}.{$fileext}";
            $filename = $filename . $size;
            
            if (file_exists_uri($filename)) {
                header('Location:' . $filename);
                die;
            } else {
                if (!is_dir('./data/' . $path . '/')) {
                    dmkdir('./data/' . $path . '/');
                }
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $uri);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                if ($from) {
                    curl_setopt($ch, CURLOPT_REFERER, $from);
                }
                $data = curl_exec($ch);
                curl_close($ch);
                $s_new_name = './data/' . $new_name;
                $fs = @fopen($s_new_name, 'w+');
                fwrite($fs, $data);
                fclose($fs);
                include_once S_ROOT . './source/function_ftp.php';
                if (ftpupload($s_new_name, $new_name)) {
                    $upload_file_name = $_SCONFIG['ftpurl'] . $new_name;
                    @unlink($s_new_name);
                } else {
                	@unlink($s_new_name);
                    runlog('ftp', "Ftp Upload {$new_name} failed.");
                }
                if ($upload_file_name) {
                    header('Location:' . $upload_file_name);
                } else {
                    header('Location:image/no_index.gif');
                }
            }
        }
    }
} else {
    die('没有需要的地址和域');
}
die;
function file_exists_uri($url)
{
    $curl = curl_init($url);
    // 不取回数据
    curl_setopt($curl, CURLOPT_NOBODY, true);
    // 发送请求
    $result = curl_exec($curl);
    $found = false;
    // 如果请求没有发送失败
    if ($result !== false) {
        // 再检查http响应码是否为200
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode == 200) {
            $found = true;
        }
    }
    curl_close($curl);
    return $found;
}