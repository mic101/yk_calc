<?php
/**
 * 20140822修改
 * 利用优酷的M3U8地址解析出视频源,m3u8地址的获取较为复杂用到类
 * http://127.0.0.1/youku.php?id=XNzM4ODk0ODcy&d=3
 * 参数d=   1，2，3  分别表示标清，高清，超清
 *
 * 不懂就乱来 www.hhtjim.com
 */
ini_set('display_errors', 1);//设置开启错误提示
error_reporting('E_ALL & ~E_NOTICE ');//错误等级提示

define('SZ', "-1,-1,-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1, -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1");

function curl_get($url)
{
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$get_url = curl_exec($ch);
	curl_close($ch);
	return $get_url;
}

class youku_m3u8
{
	public static function curl($url, $carry_header = true, $REFERER_ = 0, $add_arry_header = 0)
	{
		$ch = curl_init($url);
		if ($carry_header)
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36'));
		}
		if ($add_arry_header)
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $add_arry_header);
		}
		if ($REFERER_)
		{
			curl_setopt($ch, CURLOPT_REFERER, $REFERER_);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$get_url = curl_exec($ch);
		curl_close($ch);
		return $get_url;
	}
public static function pa($a)
{
    if (!$a) {
        return '';
    }
    $h = explode(',', SZ);
    $i = strlen($a);
    $f = 0;
    for ($e = ''; $f < $i;) {
        do {
            $c = $h[self::charCodeAt($a, $f++) & 255];
        } while ($f < $i && -1 == $c);
        if (-1 == $c) {
            break;
        }
        do {
            $b = $h[self::charCodeAt($a, $f++) & 255];
        } while ($f < $i && -1 == $b);
        if (-1 == $b) {
            break;
        }
        $e .= self::fromCharCode($c << 2 | ($b & 48) >> 4);
        do {
            $c = self::charCodeAt($a, $f++) & 255;
            if (61 == $c) {
                return $e;
            }
            $c = $h[$c];
        } while ($f < $i && -1 == $c);
        if (-1 == $c) {
            break;
        }
        $e .= self::fromCharCode(($b & 15) << 4 | ($c & 60) >> 2);
        do {
            $b = self::charCodeAt($a, $f++) & 255;
            if (61 == $b) {
                return $e;
            }
            $b = $h[$b];
        } while ($f < i && -1 == $b);
        if (-1 == $b) {
            break;
        }
        $e .= self::fromCharCode(($c & 3) << 6 | $b);
    }
    return $e;
}
	public static function charCodeAt($str, $index)
	{
		$charCode = array();
		$key = md5($str);
		$index = $index + 1;
		if (isset($charCode[$key]))
		{
			return $charCode[$key][$index];
		}
		$charCode[$key] = unpack('C*', $str);
		return $charCode[$key][$index];
	}
	public static function charAt($str, $index = 0)
	{
		return substr($str, $index, 1);
	}
	public static function fromCharCode($codes)
	{
		if (is_scalar($codes))
		{
			$codes = func_get_args();
		}
		$str = '';
		foreach ($codes as $code)
		{
			$str .= chr($code);
		}
		return $str;
	}
	public static function yk_e($a, $c)
	{
		for ($f = 0, $i, $e = '', $h = 0; 256 > $h; $h++)
		{
			$b[$h] = $h;
		}
		for ($h = 0; 256 > $h; $h++)
		{
			$f = (($f + $b[$h]) + self::charCodeAt($a, $h % strlen($a))) % 256;
			$i = $b[$h];
			$b[$h] = $b[$f];
			$b[$f] = $i;
		}
		for ($q = ($f = ($h = 0)); $q < strlen($c); $q++)
		{
			$h = ($h + 1) % 256;
			$f = ($f + $b[$h]) % 256;
			$i = $b[$h];
			$b[$h] = $b[$f];
			$b[$f] = $i;
			$e .= self::fromCharCode(self::charCodeAt($c, $q) ^ $b[($b[$h] + $b[$f]) % 256]);
		}
		return $e;
	}
	public static function yk_d($a)
	{
		$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
		if (!$a)
		{
			return '';
		}
		$f = strlen($a);
		$b = 0;
		for ($c = ''; $b < $f;)
		{
			$e = self::charCodeAt($a, $b++) &255;
			if ($b == $f)
			{
				$c .= self::charAt($str, $e >> 2);
				$c .= self::charAt($str, ($e &3) << 4);
				$c .= '==';
				break;
			}
			$g = self::charCodeAt($a, $b++);
			if ($b == $f)
			{
				$c .= self::charAt($str, $e >> 2);
				$c .= self::charAt($str, ($e &3) << 4 | ($g &240) >> 4);
				$c .= self::charAt($str, ($g &15) << 2);
				$c .= '=';
				break;
			}
			$h = self::charCodeAt($a, $b++);
			$c .= self::charAt($str, $e >> 2);
			$c .= self::charAt($str, ($e &3) << 4 | ($g &240) >> 4);
			$c .= self::charAt($str, ($g &15) << 2 | ($h &192) >> 6);
			$c .= self::charAt($str, $h &63);
		}
		return $c;
	}

	public static function get_m3u8_urls($youku_ID)
	{
		$m3u8_urls = array();
		$video_info = self::curl('http://v.youku.com/player/getPlayList/VideoIDS/' . $youku_ID . '/Pf/4/ctype/12/ev/1');
		$obj = json_decode($video_info);
		$vid = $obj->data[0]->videoid;
		$oip = $obj->data[0]->ip;
		$epdata = $obj->data[0]->ep;
		$youku_m3u8 = self::_calc_ep2($vid, $epdata);
		$after_ep_part = $youku_m3u8['ep'] . '&token=' . $youku_m3u8['token'] . '&ctype=12&ev=1&oip=' . $oip . '&sid=' . $youku_m3u8['sid'];
		$m3u8_url = 'http://pl.youku.com/playlist/m3u8?vid=' . $vid . '&type=mp4&ep=' . $after_ep_part;
		array_push($m3u8_urls, $m3u8_url);
		$m3u8_low = 'http://pl.youku.com/playlist/m3u8?vid=' . $vid . '&type=flv&ep=' . $after_ep_part;
		array_push($m3u8_urls, $m3u8_low);
		echo json_encode($m3u8_urls);
		return $m3u8_urls;
	}
	public static function _calc_ep2($vid, $ep)
	{
		$papa = self::pa($ep);
		$e_code = self::yk_e('becaf9be', $papa);
		$s_t = explode('_', $e_code);
		$sid = $s_t[0];
		$token = $s_t[1];
		$temp =  $sid . '_' . $vid . '_' . $token;
		$new_ep = self::yk_e('bf7e5f01', $sid . '_' . $vid . '_' . $token);
		//$new_ep = self::yk_e('bf7e5f01',"943585698860812a4b9b7_199285345_4954");
		$new_ep = self::yk_d($new_ep);
		$new_ep = urlencode($new_ep); 
		return array('ep' => $new_ep,
			'token' => $token,
			'sid' => $sid,
			);
	}
}
function get_youku_address_list_by_id($id) 
{
	/*
	$data = 'http://v.youku.com/player/getPlayList/VideoIDS/' . $id; //视频信息的json
	$data = curl_get($data);
	$obj = json_decode($data, 1);
	$title = $obj['data'][0]['title']; //根据信息获取视频名称
	*/
	$u = youku_m3u8::get_m3u8_urls($id); //m3u8地址url
	return $u;
}
get_youku_address_list_by_id(199285345);
?>
