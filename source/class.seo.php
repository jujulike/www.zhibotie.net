<?php

class SeoTools
{
    public function friendlyURL($string, $replacement = '-') {
        $map = array(
            '/à|á|å|â|ä/' => 'a',
            '/è|é|ê|ẽ|ë/' => 'e',
            '/ì|í|î/' => 'i',
            '/ò|ó|ô|ø/' => 'o',
            '/ù|ú|ů|û/' => 'u',
            '/ç|č/' => 'c',
            '/ñ|ň/' => 'n',
            '/ľ/' => 'l',
            '/ý/' => 'y',
            '/ť/' => 't',
            '/ž/' => 'z',
            '/š/' => 's',
            '/æ/' => 'ae',
            '/ö/' => 'oe',
            '/ü/' => 'ue',
            '/Ä/' => 'Ae',
            '/Ü/' => 'Ue',
            '/Ö/' => 'Oe',
            '/ß/' => 'ss',
        	'/&nbsp;/'=>' ',
        	'/　/'=>'',
        	'/～|·|！|@|#|￥|%|…|&|×|（|）|-|\+|=|『|【|』|】|、|:|；|“|”|\'|《|，|》|。|？|\/|—|_|：|√|＜|°|丶|＞|－|★|｜|│|‖|ˇ/'=>' ',
         	'/[^\w\s\x80-\xff]/' => ' ',
            '/\\s+/' => $replacement
        );
        $string = trim($string);
        $string = preg_replace(array_keys($map), array_values($map), $string);
       	$string = preg_replace('/\\s+/',$replacement, strtolower($string));
       	$string = trim($string,$replacement);
        return $string;
    }
    
    //判断email
	public function isEmail($mailAddr)
	{
		return strlen($mailAddr) > 6 && preg_match("/^[\w\-\.]+@[\w\-]+(\.\w+)+$/", $mailAddr);

	}
    //过滤用户
    public function CheckUser($string,$replacement = '_')
    {
        $map = array(
            '/à|á|å|â|ä/' => 'a',
            '/è|é|ê|ẽ|ë/' => 'e',
            '/ì|í|î/' => 'i',
            '/ò|ó|ô|ø/' => 'o',
            '/ù|ú|ů|û/' => 'u',
            '/ç|č/' => 'c',
            '/ñ|ň/' => 'n',
            '/ľ/' => 'l',
            '/ý/' => 'y',
            '/ť/' => 't',
            '/ž/' => 'z',
            '/š/' => 's',
            '/æ/' => 'ae',
            '/ö/' => 'oe',
            '/ü/' => 'ue',
            '/Ä/' => 'Ae',
            '/Ü/' => 'Ue',
            '/Ö/' => 'Oe',
            '/ß/' => 'ss',
        	'/&nbsp;/'=>' ',
        	'/　/'=>'',
        	'/～|·|！|@|#|￥|%|…|&|×|（|）|-|\+|=|『|【|』|】|、|:|；|“|”|\'|《|，|》|。|？|\/|—|_|：|√|＜|°|丶|＞|－|★|｜|│|‖|ˇ/'=>' ',
         	'/[^\w\s\x80-\xff]/' => ' ',
            '/\\s+/' => $replacement
        );
        $string = trim($string);
        $string = preg_replace(array_keys($map), array_values($map), $string);
       	$string = preg_replace('/\\s+/',$replacement, strtolower($string));
       	$string = trim($string,$replacement);
        return $string;
    }
    
    public function mktitle($string, $replacement = '_')
    {
        $map = array(
            '/à|á|å|â|ä/' => 'a',
            '/è|é|ê|ẽ|ë/' => 'e',
            '/ì|í|î/' => 'i',
            '/ò|ó|ô|ø/' => 'o',
            '/ù|ú|ů|û/' => 'u',
            '/ç|č/' => 'c',
            '/ñ|ň/' => 'n',
            '/ľ/' => 'l',
            '/ý/' => 'y',
            '/ť/' => 't',
            '/ž/' => 'z',
            '/š/' => 's',
            '/æ/' => 'ae',
            '/ö/' => 'oe',
            '/ü/' => 'ue',
            '/Ä/' => 'Ae',
            '/Ü/' => 'Ue',
            '/Ö/' => 'Oe',
            '/ß/' => 'ss',
        	'/&nbsp;/'=>' ',
        	'/　/'=>'',
        	'/～|·|！|@|#|￥|%|…|&|×|（|）|-|\+|=|『|【|』|】|、|:|；|“|”|\'|《|，|》|。|？|\/|—|_|：|√|＜|°|丶|＞|－|★|｜|│|‖|ˇ/'=>' ',
         	'/[^\w\s^.\x80-\xff]/' => ' ',
            '/\\s+/' => $replacement
        );
        $string = trim($string);
        $string = preg_replace(array_keys($map), array_values($map), $string);
       	$string = preg_replace('/\\s+/',$replacement, strtolower($string));
       	$string = trim($string,$replacement);
        return $string;
    }
    
	// 全角替换成半角
    public function Qj2bj($string)
    {
		$qj2bj = array(
	        '１' => '1', '２' => '2', '３' => '3', '４' => '4', '５' => '5',
	        '６' => '6', '７' => '7', '８' => '8', '９' => '9', '０' => '0',
	        'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd', 'ｅ' => 'e',
	        'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i', 'ｊ' => 'j',
	        'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n', 'ｏ' => 'o',
	        'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's', 'ｔ' => 't',
	        'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x', 'ｙ' => 'y',
	        'ｚ' => 'z', 'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D',
	        'Ｅ' => 'E', 'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I',
	        'Ｊ' => 'J', 'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N',
	        'Ｏ' => 'O', 'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S',
	        'Ｔ' => 'T', 'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X',
	        'Ｙ' => 'Y', 'Ｚ' => 'Z', '　' => ' '
	    );
	    return strtr($string, $qj2bj);
    }
    
    public function DelNbsp($string)
    {
        $map = array(
        	'/&nbsp;/'=>' ',
        	'/\?/'=>' ',
        	'/　/'=>'',
        );
        $string = preg_replace(array_keys($map), array_values($map), $string);
    	$string = preg_replace('/\\s+/','',$string);
    	return $string;
    }
    public function getZipcode($str)
    {
    	$pattern = "/[0-9]{1}(\d+){4,5}/";
    	preg_match_all($pattern,$str,$zipcodeArr); 
    	if(empty($zipcodeArr[0]))
		{
			return '';
		}
		else {
			return $zipcodeArr[0][0];
		}
    
    }

    public function checkZipcode($str)
    {
    	$strwidth=strlen($str);
    	$zipcode=$str;
    	switch($strwidth)
    	{
    		case 4:$zipcode="00".$str;break;
    		case 5:$zipcode="0".$str;break;
    		case 6:$zipcode=$str;break;
    		default:$zipcode="";break;
    	}
    	return $zipcode;
    }
    
	public function getEmail($str) { 
		$pattern = "/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/i"; 
		preg_match_all($pattern,$str,$emailArr); 
		if(empty($emailArr[0]))
		{
			return '';
		}
		else {
			$email_str=implode(",", $emailArr[0]);
			return $email_str;
		}
	} 
	public function getUrl($str)
	{
		$pattern = "/(http:\/\/|https:\/\/|ftp:\/\/)?([\w:\/\.\?=&-_]+)/is"; 
		preg_match_all($pattern,$str,$urlArr);
		if(empty($urlArr[0]))
		{
			return '';
		}
		else {
			$url_str=implode(",", $urlArr[0]);
			$url_str=preg_replace('/(http:\/\/|https:\/\/|ftp:\/\/)/','',$url_str);
			return $url_str;
		}

		
	}
	public function match_links($document) {  
	$match=array();  
	   preg_match_all("'<\s*a\s.*?href\s*=\s*([\"\'])?(?(1)(.*?)\\1|([^\s\>]+))[^>]*>?(.*?)</a>'isx",$document,$links);                        
	   while(list($key,$val) = each($links[2])) {
	       if(!empty($val))
	           $match['link'][] = $val;
	   }
	   while(list($key,$val) = each($links[3])) {
	       if(!empty($val))
	           $match['link'][] = $val;
	   }        
	   while(list($key,$val) = each($links[4])) {
	       if(!empty($val))
	           $match['content'][] = $val;
	   }
	   while(list($key,$val) = each($links[0])) {
	       if(!empty($val))
	           $match['all'][] = $val;
	   }                
	   return $match;
	}
    public function DelNoStr($string)
    {
        $map = array(
            '/à|á|å|â|ä/' => 'a',
            '/è|é|ê|ẽ|ë/' => 'e',
            '/ì|í|î/' => 'i',
            '/ò|ó|ô|ø/' => 'o',
            '/ù|ú|ů|û/' => 'u',
            '/ç|č/' => 'c',
            '/ñ|ň/' => 'n',
            '/ľ/' => 'l',
            '/ý/' => 'y',
            '/ť/' => 't',
            '/ž/' => 'z',
            '/š/' => 's',
            '/æ/' => 'ae',
            '/ö/' => 'oe',
            '/ü/' => 'ue',
            '/Ä/' => 'Ae',
            '/Ü/' => 'Ue',
            '/Ö/' => 'Oe',
            '/ß/' => 'ss',
        	'/&nbsp;/'=>' ',
        	'/　/'=>'',
        	'/～|·|！|@|#|￥|%|…|&|×|（|）|-|\+|=|『|【|』|】|、|:|；|“|”|’|《|，|》|。|？|\/|—|_|‘|：|√|＜|°|丶|ˇ/'=>' ',
         	'/[^\w\s\x80-\xff]/' => ' ',
           // '/\\s+/' => $replacement
        );
        $string = trim($string);
        $string = preg_replace(array_keys($map), array_values($map), $string);
       	$string = preg_replace('/\\s+/','',$string);
       	$string = trim($string,'');
        return $string;
    }
    public function UrlToStr($string, $find = '/-/')
    {	
        $string = preg_replace($find," ", strtolower($string));
        $string = trim($string);
        return $string;
    }
    
    public function GetPyLetter($str)
    {
    	return substr(pinyin($str, $ucfirst=true), 0,1);
    }
    public function GetPinYin($str)
    {
    	return strtolower(pinyin($str, $ucfirst=true));
    }
	
	public function DelCode($str)
    {
    	
		$search = array("'<script[^>]*?>.*?</script>'si",	// strip out javascript
						"'<[\/\!]*?[^<>]*?>'si",			// strip out html tags
						"'([\r\n])[\s]+'",					// strip out white space
						"'&(quot|#34|#034|#x22);'i",		// replace html entities
						"'&(amp|#38|#038|#x26);'i",			// added hexadecimal values
						"'&(lt|#60|#060|#x3c);'i",
						"'&(gt|#62|#062|#x3e);'i",
						"'&(nbsp|#160|#xa0);'i",
						"'&(iexcl|#161);'i",
						"'&(cent|#162);'i",
						"'&(pound|#163);'i",
						"'&(copy|#169);'i",
						"'&(reg|#174);'i",
						"'&(deg|#176);'i",
						"'&(#39|#039|#x27);'",
						"'&(euro|#8364);'i",				// europe
						"'&a(uml|UML);'",					// german
						"'&o(uml|UML);'",
						"'&u(uml|UML);'",
						"'&A(uml|UML);'",
						"'&O(uml|UML);'",
						"'&U(uml|UML);'",
						"'&szlig;'i",
						);
		$replace = array("",
							"",
							"\\1",
							"\"",
							"&",
							"<",
							">",
							" ",
							chr(161),
							chr(162),
							chr(163),
							chr(169),
							chr(174),
							chr(176),
							chr(39),
							chr(128),
							"?",
							"?",
							"?",
							"?",
							"?",
							"?",
							"?",
						);
		$str = preg_replace($search,$replace,$str);
		return trim($str);
    }
    
	
	public function CutStr($sourcestr,$cutlength)
	{
	   $returnstr='';
	   $i=0;
	   $n=0;
	   $sourcestr=rtrim(SeoTools::DelCode($sourcestr));
	   $str_length=strlen($sourcestr);//字符串的字节数
		while (($n<$cutlength) and ($i<=$str_length))
		{
	      $temp_str=substr($sourcestr,$i,1);
	      $ascnum=Ord($temp_str);//得到字符串中第$i位字符的ascii码
	      if ($ascnum>=224)    //如果ASCII位高与224，
	      {
			$returnstr=$returnstr.substr($sourcestr,$i,3); //根据UTF-8编码规范，将3个连续的字符计为单个字符       
	         $i=$i+3;            //实际Byte计为3
	         $n++;            //字串长度计1
	      }
	      elseif ($ascnum>=192) //如果ASCII位高与192，
	      {
	         $returnstr=$returnstr.substr($sourcestr,$i,2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
	         $i=$i+2;            //实际Byte计为2
	         $n++;            //字串长度计1
	      }
	      elseif ($ascnum>=65 && $ascnum<=90) //如果是大写字母，
	      {
	         $returnstr=$returnstr.substr($sourcestr,$i,1);
	         $i=$i+1;            //实际的Byte数仍计1个
	         $n++;            //但考虑整体美观，大写字母计成一个高位字符
	      }
	      else                //其他情况下，包括小写字母和半角标点符号，
	      {
	         $returnstr=$returnstr.substr($sourcestr,$i,1);
	         $i=$i+1;            //实际的Byte数计1个
	         $n=$n+0.5;        //小写字母和半角标点等与半个高位字符宽...
	      }
		}
		if ($str_length>$i){
			$returnstr = $returnstr . ".";//超过长度时在尾处加上省略号
		}
	    return htmlspecialchars($returnstr);
	}
	
	public function CreateTagLink($tags_str)
	{
		$tags_array=explode(",",$tags_str);
		$link_tpl="";
		foreach($tags_array as $v)
		{
			$tag=trim($v);
			$alias=SeoTools::friendlyURL($tag);
		//	$link_tpl.='<a href="/s/'.urlencode($alias).'" title="'.$tag.'">'.$tag.'</a>&nbsp;&nbsp;';
			$link_tpl.=$tag.'&nbsp;&nbsp;';
		}
		return $link_tpl;
	}
	
}




/**
 * 中文转拼类
 *
 * @author  Lukin <my@lukin.cn>
 * @date    2011-01-25 10:44
 */
class PinYin {
    // 码表
    private $fp  = null;
    private $dat = 'pinyin.dat';

    public function __construct(){
        $this->dat = dirname(__FILE__).'/'.$this->dat;
        if (is_file($this->dat)) {
            $this->fp = fopen($this->dat, 'rb');
        }
    }
    /**
     * 转拼音
     *
     * @param string $str   汉字
     * @param bool $ucfirst 首字母大写
     * @param bool $polyphony 忽略多读音
     * @return string
     */
    public function encode($str, $ucfirst=true, $polyphony=true) {
        $ret = ''; $len = mb_strlen($str, 'UTF-8');
        for ($i = 0; $i < $len; $i++) {
            $py = $this->pinyin(mb_substr($str, $i, 1, 'UTF-8'));
            if ($ucfirst && strpos($py,',') !== false) {
                $pys = explode(',', $py); 
                $ret.= implode(',', array_map('ucfirst', ($polyphony ? array_slice($pys, 0, 1) : $pys)));
            } else {
                $ret.= $ucfirst ? ucfirst($py) : $py;
              
            }
        }
        
        return $ret;
    }
    /**
     * 汉字转十进制
     *
     * @param string $word
     * @return number
     */
    private function char2dec($word) {
        $bins  = '';
        $chars = str_split($word);
        foreach($chars as $char) $bins.= decbin(ord($char));
        $bins = preg_replace('/^.{4}(.{4}).{2}(.{6}).{2}(.{6})$/', '$1$2$3', $bins);
        return bindec($bins);
    }
    /**
     * 单个字转拼音
     *
     * @param string $char  汉字
     * @return string
     */
    public function pinyin($char){
        if (strlen($char) == 3 && $this->fp) {
            $offset = $this->char2dec($char);
            // 判断 off 值
            if ($offset >= 0) {
                fseek($this->fp, ($offset - 19968) << 4, SEEK_SET);
                return trim(fread($this->fp, 16));
            }
        }
        return $char;
    }

    public function __destruct() {
        if ($this->fp) {
            fclose($this->fp);
        }
    }
}
