<?php
header("Content-type:text/html;charset=utf-8");
include_once './common.php';
include_once S_ROOT.'./source/oss/conf.inc.php';
include_once S_ROOT.'./source/oss/sdk.class.php';
include_once S_ROOT.'./source/oss/oss.php';
$_GET = saddslashes($_GET);


$allow_path = array (
    "tianya", "baidu", "douban", "doubanhead", "baiduhead", "tianyahead" 
);
$allow_host = array (
    "zhibotie.net" 
);
$from = isset($_GET['from']) ? strtolower($_GET['from']) : "";
$uri = isset($_GET['uri']) ? urldecode($_GET['uri']) : "";
$remote = isset($_GET['remote']) ? trim($_GET['remote']) : 0;
$path = isset($_GET['path']) ? strtolower(trim($_GET['path'])) : "tianya";
$size = isset($_GET['size']) ? $_GET['size'] : '';
$size = $size ? "!{$size}" : "";
// 判断$path
if (!$path || !in_array($path, $allow_path))
    exit("path error!!");

$fileext = "jpg";
if ($path == "baiduhead" || $path == "tianyahead") {
    $fileext = "jpg";
}

if ($from) {
    $fromlist = explode("/", $uri);
    $fromlist_new = explode(".", $fromlist[2]);
    $from_new = "http://*.{$fromlist_new[1]}.{$fromlist_new[2]}";
    $from = ($from == $from_new) ? $from : $from_new;
}

if ($uri) {
    if (!$remote) {
    	
    } else {
        // 加入 CACHE
        $path = "cache/{$path}";
        $urlmd5 = strtoupper(md5($uri));
        $fileext = ($fileext) ? $fileext : strtolower(trim(substr(strrchr($uri, '.'), 1)));
        $newC = substr($urlmd5, 0, 2) . "/" . substr($urlmd5, 2, 2);
        $path = "{$path}/{$newC}";
        $filename = "http://captainamerica.oss.aliyuncs.com/{$path}/{$urlmd5}.{$fileext}";
        $new_name = "{$path}/{$urlmd5}.{$fileext}";
        $filename = $filename . $size;
        $oss = new oss();
        $data = $oss->is_object_exist($new_name);
        if ($data['status'] === 200) {
            header("Location:" . $filename);
            exit();
        }
        if (!is_dir("./data/" . $path . "/")) {
            dmkdir("./data/" . $path . "/");
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if ($from) {
            curl_setopt($ch, CURLOPT_REFERER, $from);
        }
        ob_start();
        curl_exec($ch);
        $image = ob_get_contents();
        ob_end_clean();
        curl_close($ch);
        $s_new_name = "./data/" . $new_name;
        file_put_contents($s_new_name, $image);
        unset($data);
        $data = $oss->upload_by_file($new_name, $s_new_name);
        if($data['status'] === 200){
            unlink($s_new_name);
        }
        header("Location:" . $filename);
        exit();
    }
}
