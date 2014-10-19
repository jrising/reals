<?php

/***** PHP 5.* Functions *****/

/*
 * Construct a GET query (from http://us2.php.net/function.http_build_query)
 */
if(!function_exists('http_build_query')) {
	function http_build_query($formdata, $numeric_prefix = null, $key = null) {
		$res = array();
		foreach ((array)$formdata as $k=>$v) {
			$tmp_key = urlencode(is_int($k) ? $numeric_prefix.$k : $k);
			if ($key) $tmp_key = $key.'['.$tmp_key.']';
			if ( is_array($v) || is_object($v) ) {
				$res[] = http_build_query($v, null /* or $numeric_prefix if you want to add numeric_prefix to all indexes in array*/, $tmp_key);
			} else {
				$res[] = $tmp_key."=".urlencode($v);
			}
			/*
           If you want, you can write this as one string:
           $res[] = ( ( is_array($v) || is_object($v) ) ? http_build_query($v, null, $tmp_key) : $tmp_key."=".urlencode($v) );
			*/
		}
		$separator = ini_get('arg_separator.output');
		return implode($separator, $res);
	}
}

/*
 * Combine two arrays so one respresents the keys and one the values
 * Stolen from php.net
 */
if (!function_exists('array_combine')) {
	function array_combine($keys, $values) {
		$newarr = array();
		foreach ($keys as $indexnum => $key) {
			$newarr[$key] = $values[$indexnum];
		}

		return $newarr;
	}
}


/*
 * Convert a string to an array
 */
if (!function_exists("str_split")) {
	function str_split($str,$length = 1) {
		if ($length < 1) return false;
		$strlen = mb_strlen($str);
		$ret = array();
		for ($i = 0; $i < $strlen; $i += $length) {
			$ret[] = substr($str,$i,$length);
		}
		return $ret;
	}
}

/*
 * Case-insensitive search for a substring
 */
if (!function_exists("stripos")) {
	function stripos($str, $needle, $offset=0) {
		return mb_strpos(strtolower($str), strtolower($needle), $offset);
	}
}

// make array with keys from one array, values from another
// move into common.php?
if(!function_exists('array_combine')) {
	function array_combine($a, $b) {
		$c = array();
		$at = array_values($a);
		$bt = array_values($b);
		foreach($at as $key=>$aval) $c[$aval] = $bt[$key];
		return $c;
	}
}

?>