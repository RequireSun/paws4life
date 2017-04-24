<?php
/**
 * Created by IntelliJ IDEA.
 * User: kelvinsun
 * Date: 2017/3/16
 * Time: 0:34
 */

if (!function_exists('removeXSS')) {
	/**
	 * xss过滤函数
	 * @param string $val 待过滤的文本
	 * @param string $type 文本内容类型
	 * @return string
	 */
	function removeXSS($val, $type='text') {
		switch ($type) {
			case 'url':
				return preg_replace('/([^a-zA-Z0-9_\.?=&#|\/]+)/i','',$val);
				break;
			case 'text':
				$val = preg_replace('/([\x00-\x08]|[\x0b-\x0c]|[\x0e-\x19])/', '', $val);
				$val = str_replace(array('(',')','u003c','u003e'),array('(',')','',''), $val);
				return htmlspecialchars($val);
				break;
			case 'num':
				return preg_replace('/([^0-9Ee+\.]+)/i','',$val);
				break;
			case 'email':
				return preg_replace('/([^a-zA-Z0-9_\.@]+)/i','',$val);
				break;
			case 'phone':
				return preg_replace('/([^0-9\-]+)/i','',$val);
				break;
			case 'betContent':
				return preg_replace('/([^a-zA-Z0-9_\.,:^?=&#|\/]+)/i','',$val);
				break;
			default:
				break;
		}
	}
}
if (!function_exists('encodeJson')) {
	/**
	 * json_encode并转换成unicode编码
	 * @param array $arr
	 * @return string
	 */
	function encodeJson($arr) {
		require_once APPPATH . 'libraries/Json.php';
		$arr = isJson($arr);
		$str = json_encode($arr);
		return $str;
	}

	function isJson($v) {
		if (is_array($v)) {
			foreach ($v as $key => $value) {
				$v[$key] = isJson($value);
			}
			return $v;
		} elseif (is_string($v)) {
			return toUnicode($v);
		} else {
			return $v;
		}
	}
	/**
	 * @param $str
	 * @return string
	 */
	function toUnicode($str) {
		return transformCharset($str, "utf-8");
	}
	/**
	 * @param $str
	 * @param $charset
	 * @return string
	 */
	function transformCharset($str, $charset)
	{
		return @iconv(mb_detect_encoding($str, array("ASCII", 'UTF-8', "GB2312", "GBK", "BIG5")), $charset, $str);
	}
}
