<?php
/**
 * @param 内裤叔叔 分离 live.php 文件
 * @param 豆瓣获取下一页
 */


if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$memcache = new cache_memcache();
$tid = $_POST[ 'tid' ];
$maxpage = $_POST[ 'maxpage' ];
$thispage = $_POST[ 'thispage' ];
//是否锁住判断，第一个用户进来没有锁住，其他人进来锁住
$douban = $memcache->get("douban_{$tid}");
if(!$douban){
	//锁住
	$memcache ->save( "douban_{$tid}", $tid );
	$subject = $_SGLOBAL[ 'db' ] -> fetch_array( $_SGLOBAL[ 'db' ] -> query( 'SELECT * FROM ' . tname( 'threads' ) . " WHERE tid='$tid'" ) );//不是最后一页
	if( ( $maxpage != ( $thispage + 1 ) ) )
	{
		$nextpagesize = ( $thispage + 1 ) * 100 ;
		$url = $subject[ 'fromurl' ] . "?start={$nextpagesize}";
		//检测地址是否可用
		if( check_url_is_ok( $url ) )
		{
			$array = _getDouban( $url );
			//如果这一页是100个地址的话，就获取否则不获取
			if( count( $array[ 'reply' ] ) >= 100 )
			{
				$viewnum = 1;
				foreach( $array[ 'reply' ] as $key => $v )
				{
				    if($v[ 'hash' ]){
    				    if($_SC['getothercommont']){
    				        $newlist[ 'subject' ] = saddslashes( trim( $subject[ 'subject' ] ) );
    				        $newlist[ 'message' ] = html2bbcode( $v[ 'message' ] );
    				        $newlist[ 'author' ] = saddslashes( trim( $v[ 'author' ] ) );
    				        $newlist[ 'fid' ] = 1;
    				        $newlist[ 'tid' ] = $tid;
    				        $newlist[ 'frist' ] = 0;
    				        $newlist[ 'head' ] = trim( $v[ 'head' ] );
    				        $newlist[ 'status' ] = saddslashes( trim( $v[ 'status' ] ) );
    				        $newlist[ 'hash' ] = trim( $v[ 'hash' ] );
    				        $newlist[ 'lastpost' ] = trim( $v[ 'lastpost' ] );
    				        inserttable( 'posts', $newlist );
    				        $last[] = $newlist;
    				    }else{
        					if($subject['hash'] == $v['hash']){
        						$newlist[ 'subject' ] = saddslashes( trim( $subject[ 'subject' ] ) );
        						$newlist[ 'message' ] = html2bbcode( $v[ 'message' ] );
        						$newlist[ 'author' ] = saddslashes( trim( $v[ 'author' ] ) );
        						$newlist[ 'fid' ] = 1;
        						$newlist[ 'tid' ] = $tid;
        						$newlist[ 'frist' ] = 0;
        						$newlist[ 'head' ] = trim( $v[ 'head' ] );
        						$newlist[ 'status' ] = saddslashes( trim( $v[ 'status' ] ) );
        						$newlist[ 'hash' ] = trim( $v[ 'hash' ] );
        						$newlist[ 'lastpost' ] = trim( $v[ 'lastpost' ] );
        						inserttable( 'posts', $newlist );
        						$last[] = $newlist;
        					}
    				    }
    					$viewnum ++;
				    }
				}


				if( $array )
				{
					$views = $subject['views'] + $viewnum;
					$replies = $subject['replies'] + $viewnum;
					$nextpage = $thispage + 1;
					$_SGLOBAL[ 'db' ] -> query( 'UPDATE ' . tname( 'threads' ) . " SET thispage='$nextpage',views='$views',replies='$replies' WHERE tid='$tid'" );
					echo 'Get nextpage Succeed.';
				}
			}
			else
			{
				echo 'Enough 100';
			}
		}
		else
		{
			echo '404 401 202 501 505';
		}
	}
	//释放锁定
	$memcache ->delete("douban_{$tid}");
}
else{
	echo "lock! someone used";
}
exit();