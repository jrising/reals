<?php

function isdebug() {
	return isset($GLOBALS['_DEBUG']) && $GLOBALS['_DEBUG'];
}

function reals($path) {
	$full = dirname(__FILE__) . '/' . $path;
	if (is_dir($full) && file_exists($full . '/base.php'))
		$full .= '/base.php';

	return $full;
}

?>