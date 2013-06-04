<?php

/**
 * @author 内裤叔叔
 * @param 格式化时间类，用户输出等操作；
 * @param ex. echo DateFormat::diff('1310455823');
 * @copyright 2011
 */


class dateService{
	private static $_DIFF_FORMAT = array(
		'DAY' 			=> '%s天前',
		'DAY_HOUR'		=> '%s天%s小时前',
		'HOUR' 			=> '%s小时',
		'HOUR_MINUTE' 	=> '%s小时%s分前',
		'MINUTE' 		=> '%s分钟前',
		'MINUTE_SECOND'	=> '%s分钟%s秒前',
		'SECOND'		=> '%s秒前',
	);

	/**
	 * 友好格式化时间
	 * 
	 * @param int 时间
	 * @param array $formats
	 * @return string
	 */
	public function diff($timestamp, $formats = null) 
	{
		if ($formats == null) {
			$formats = self::$_DIFF_FORMAT;
		}
		/* 计算出时间差 */
		$seconds = time() - $timestamp;
		$minutes = floor($seconds / 60);
		$hours 	 = floor($minutes / 60);
		$days 	 = floor($hours / 24);
		
		if ($days > 0 && $days <= 3) {
			$diffFormat = 'DAY';
		} elseif($days > 3) {
			$diffFormat = "";
		}else{
			$diffFormat = ($hours > 0) ? 'HOUR' : 'MINUTE';
			if ($diffFormat == 'HOUR') {
				$diffFormat .= ($minutes > 0 && ($minutes - $hours * 60) > 0) ? '_MINUTE' : '';
			} else {
				$diffFormat = (($seconds - $minutes * 60) > 0 && $minutes > 0) 
								? $diffFormat.'_SECOND' : 'SECOND';
			}
		}
		
		$dateDiff = null;
		switch ($diffFormat) {
			case 'DAY':
				$dateDiff = sprintf($formats[$diffFormat], $days);
				break;
			case 'DAY_HOUR':
				$dateDiff = sprintf($formats[$diffFormat], $days, $hours - $days * 60);
				break;
			case 'HOUR':
				$dateDiff = sprintf($formats[$diffFormat], $hours);
				break;
			case 'HOUR_MINUTE':
				$dateDiff = sprintf($formats[$diffFormat], $hours, $minutes - $hours * 60);
				break;
			case 'MINUTE':
				$dateDiff = sprintf($formats[$diffFormat], $minutes);
				break;
			case 'MINUTE_SECOND':
				$dateDiff = sprintf($formats[$diffFormat], $minutes, $seconds - $minutes * 60);
				break;
			case 'SECOND':
				$dateDiff = sprintf($formats[$diffFormat], $seconds);
				break;
			default:
				$dateDiff = date("Y-m-d H:i:s",$timestamp);
				break;
		}
		return $dateDiff;
	}
}


?>