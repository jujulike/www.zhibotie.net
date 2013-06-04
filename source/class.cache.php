<?php
/*********************************************************************************
 * InitPHP 3.2.2 国产PHP开发框架  Dao-filecahce 文件缓存
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By initphp.com
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:zhuli
 * $Dtime:2012-11-27 
***********************************************************************************/
class filecacheInit {
	
	private $cache_path = '.'; //缓存路径
	private $cache_key  = ""; //MD5信息
	
	/**
	 * 文件缓存-设置缓存
	 * 设置缓存名称，数据，和缓存时间
	 * @param string $filename 缓存名
	 * @param array  $data     缓存数据
	 */
	public function set_cache($filename, $data, $time = 0) {
		 $filename = $this->get_cache_filename($filename);
		 @file_put_contents($filename, '<?php exit;?>' . time() .'('.$time.')' .  json_encode($data));
		 clearstatcache();
		 return true;
	}
	
	/**
	 * 文件缓存-获取缓存
	 * 获取缓存文件，分离出缓存开始时间和缓存时间
	 * 返回分离后的缓存数据，解序列化
	 * @param  string $filename 缓存名
	 * @return array
	 */
	public function get_cache($filename) {
		$filename = $this->get_cache_filename($filename);
		/* 缓存不存在的情况 */
		if (!file_exists($filename)) return false; 
		$data = file_get_contents($filename); //获取缓存
		/* 缓存过期的情况 */
		$filetime = substr($data, 13, 10);
		$pos = strpos($data, ')');
		$cachetime = substr($data, 24, $pos - 24);
		$data  = substr($data, $pos +1);
		if ($cachetime == 0) return @json_decode($data,true);
		if (time() > ($filetime + $cachetime)) {
			@unlink($filename);
			return false; //缓存过期
		}
        return @json_decode($data,true);
	}
	
	/**
	 * 文件缓存-清除缓存
	 * 删除缓存文件
	 * @param  string $filename 缓存名
	 * @return array
	 */
	public function clear($filename) {
		$filename = $this->get_cache_filename($filename);
		if (!file_exists($filename)) return true;
		@unlink($filename);
		return true;
	}
	
	/**
	 * 文件缓存-清除全部缓存
	 * 删除整个缓存文件夹文件，一般情况下不建议使用
	 * @param  string $filename 缓存名
	 * @return array
	 */
	public function clear_all() {
		@set_time_limit(3600);
		$path = opendir($this->cache_path);		
		while (false !== ($filename = readdir($path))) {
			if ($filename !== '.' && $filename !== '..') {
   				@unlink($this->cache_path . '/' .$filename);
			}
		}
		closedir($path);
		return true;
	}
	
	/**
	 * 设置文件缓存路径
	 * 在配置文件中配置了该缓存文件
	 * $InitPHP_conf['cache']['filepath'] = 'data/filecache';
	 * @param  string $path 路径
	 * @param string $key 需要md5信息
	 * @return string
	 */
	public function set_cache_path($path,$key = "") {
		$this->cache_key = $key;
		return $this->cache_path = $path;
	}
	
	/**
	 * 获取缓存文件名
	 * @param  string $filename 缓存名
	 * @return string
	 */
	private function get_cache_filename($filename) {
		$tidmd5 = $this->cache_key? substr(md5($this->cache_key), 3) : substr(md5($this->cache_key), 3);
		$fulldir = $this->cache_path .'/'. $tidmd5[0].'/'.$tidmd5[1].'/'.$tidmd5[2].'/';
		$filename = $fulldir .md5($filename) . '.php';
		if(!is_file($filename)){
			if(!is_dir($fulldir)){
				$this->dmkdir($fulldir);
			}
		}
		return $filename;
	}
	
	public function get_cache_path(){
		$tidmd5 = substr(md5($this->cache_key), 3);
		$fulldir = $this->cache_path .'/'. $tidmd5[0].'/'.$tidmd5[1].'/'.$tidmd5[2].'/';
		return $fulldir;
	}
	
	/**
	 * 创建目录
	 * @param string $dir 目录名称 可多层目录
	 * @param int $mode 目录权限
	 * @param bool $makeindex 是否生成index文件
	 * @return bool
	 */
	public function dmkdir($dir, $mode = 0777, $makeindex = TRUE){
		if(!is_dir($dir)) {
			dmkdir(dirname($dir), $mode, $makeindex);
			@mkdir($dir, $mode);
			if(!empty($makeindex)) {
				@touch($dir.'/index.html'); @chmod($dir.'/index.html', 0777);
			}
		}
		return true;
	}
}