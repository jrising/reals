<?php

require_once("settings.php");

// $server overrides script directory for making globals
function GetDefaultGlobals($script) {
	global $kBaseServerName, $kServerEmailAddr;

	$fullpath = realpath($script);

	if (preg_match('@/var/www/([^/]*)/(.*)@', $fullpath, $matches)) {
		$server = $matches[1];
		$servdir = "/var/www/$server/";
		if ($server == 'public_html') { // do after defining servdir
			$server = 'www';
		}
		$request = '/' . $matches[2];
	} else {
		$server = 'www';
		$servdir = "/var/www/public_html/";
		$request = $script;
	}
	$servars = array('HTTP_HOST' => "$server.$kBaseServerName",
					 'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.9) Gecko/20050711 Firefox/1.0.5',
					 'HTTP_ACCEPT' => 'text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5',
					 'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5',
					 'HTTP_ACCEPT_ENCODING' => 'gzip,deflate',
					 'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
					 'HTTP_KEEP_ALIVE' => '300',
					 'HTTP_CONNECTION' => 'keep-alive',
					 'HTTP_COOKIE' => '',
					 'HTTP_CACHE_CONTROL' => 'max-age=0',
					 'PATH' => '/sbin:/usr/sbin:/bin:/usr/bin:/usr/X11R6/bin',
					 'SERVER_SIGNATURE' => '',
					 'SERVER_SOFTWARE' => 'Apache',
					 'SERVER_NAME' => "$server.$kBaseServerName",
					 'SERVER_ADDR' => '209.135.157.91',
					 'SERVER_PORT' => '80',
					 'REMOTE_ADDR' => '192.168.0.1', // dummy ip
					 'DOCUMENT_ROOT' => $servdir,
					 'SERVER_ADMIN' => $kServerEmailAddr,
					 'SCRIPT_FILENAME' => $fullpath,
					 'REMOTE_PORT' => '1',
					 'REMOTE_USER' => 'sales',
					 'AUTH_TYPE' => 'Basic',
					 'GATEWAY_INTERFACE' => 'CGI/1.1',
					 'SERVER_PROTOCOL' => 'HTTP/1.1',
					 'REQUEST_METHOD' => 'GET',
					 'QUERY_STRING' => '',
					 'REQUEST_URI' => $request,
					 'SCRIPT_NAME' => $request,
					 'PHP_SELF' => $request,
					 'PATH_TRANSLATED' => $fullpath,
					 'PHP_AUTH_USER' => 'sales',
					 'PHP_AUTH_PW' => 'mus1c*55',
					 'argv' => array(),
					 'argc' => 0);

	$envvars = array('SHELL' => '/bin/sh',
					 'LD_LIBRARY_PATH' => '/usr/lib:/usr/local/lib',
					 'PATH' => '/sbin:/usr/sbin:/bin:/usr/bin:/usr/X11R6/bin',
					 'PWD' => '/usr/libexec/webmin/status',
					 'LANG' => 'en_US.UTF-8',
					 'WEBMIN_CONFIG' => '/etc/webmin',
					 'SHLVL' => '3',
					 'HOME' => '/root',
					 'LOGNAME' => 'root',
					 'WEBMIN_VAR' => '/var/webmin',
					 '_' => '/sbin/initlog');					 

	$globals = array('HTTP_POST_VARS' => array(),
					 '_POST' => array(),
					 'HTTP_GET_VARS' => array(),
					 '_GET' => array(),
					 'HTTP_COOKIE_VARS' => array(),
					 '_COOKIE' => array(),
					 'HTTP_SERVER_VARS' => $servars,
					 '_SERVER' => $servars,
					 'HTTP_ENV_VARS' => $envvars,
					 '_ENV' => $envvars,
					 'HTTP_POST_FILES' => array(),
					 '_FILES' => array(),
					 '_REQUEST' => array());

	$globals['GLOBALS'] = $globals;
					 
	return $globals;
}

// only do one level of recursion
function SetGlobals($globals) {
	foreach ($globals as $var => $val) {
		if (is_array($val)) {
			foreach ($val as $subvar => $subval) {
				$GLOBALS[$var][$subvar] = $subval;
			}
			continue;
		}
		$GLOBALS[$var][$val];
	}
}


?>