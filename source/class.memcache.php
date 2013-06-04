<?php 
/**
 * @author 内裤叔叔
 * @param 用户memcache缓存类;
 */
/*********************************************************************************
 * InitPHP 2.0 国产PHP开发框架  Dao-memcached 内存缓存
*-------------------------------------------------------------------------------
* 版权所有: CopyRight By initphp.com
* 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
*-------------------------------------------------------------------------------
* $Author:zhuli
* $Dtime:2011-10-09
***********************************************************************************/
class cache_memcache {

	
	/**
	 * 连接资源对象
	 * @var string
	 */
	private $memcache = NULL;
	private static $config = array("host"=>"127.0.0.1","port"=>11211);
	
	public function __construct($config = array()){
		if(!is_array($config) || empty($config)) $config = self::$config;
		if($this->memcache === NULL){
			$this->memcache = new Memcache();
			$this->memcache->connect($config['host'],$config['port']);
		}
		return $this->memcache;
	}
	
	/**
	 * Memcache缓存-设置缓存
	 * 设置缓存key，value和缓存时间
	 * @param  string $key   KEY值
	 * @param  string $value 值
	 * @param  string $time  缓存时间
	 */
	public function save($key, $value, $time = 0) {
		return $this->memcache->set($key, $value, MEMCACHE_COMPRESSED, $time);
	}

	/**
	 * Memcache缓存-获取缓存
	 * 通过KEY获取缓存数据
	 * @param  string $key   KEY值
	 */
	public function get($key) {
		return $this->memcache->get($key);
	}

	/**
	 * Memcache缓存-清除一个缓存
	 * 从memcache中删除一条缓存
	 * @param  string $key   KEY值
	 */
	public function delete($key) {
		return $this->memcache->delete($key);
	}

	/**
	 * Memcache缓存-清空所有缓存
	 * 不建议使用该功能
	 * @return
	 */
	public function clean() {
		return $this->memcache->flush();
	}

	/**
	 * 字段自增-用于记数
	 * @param string $key  KEY值
	 * @param int    $step 新增的step值
	 */
	public function  increment($key, $step = 1) {
		return $this->memcache->increment($key, (int) $step);
	}

	/**
	 * 字段自减-用于记数
	 * @param string $key  KEY值
	 * @param int    $step 新增的step值
	 */
	public function decrement($key, $step = 1) {
		return $this->memcache->decrement($key, (int) $step);
	}

	/**
	 * 关闭Memcache链接
	 */
	public function close() {
		return $this->memcache->close();
	}

	/**
	 * 替换数据
	 * @param string $key 期望被替换的数据
	 * @param string $value 替换后的值
	 * @param int    $time  时间值
	 * @param bool   $flag  是否进行压缩
	 */
	public function replace($key, $value, $time = 0, $flag = false) {
		return $this->memcache->replace($key, $value, false, $time);
	}

	/**
	 * 获取Memcache的版本号
	 */
	public function getVersion() {
		return $this->memcache->getVersion();
	}

	/**
	 * 获取Memcache的状态数据
	 */
	public function getStats() {
		return $this->memcache->getStats();
	}
	
}