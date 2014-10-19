<?php

define('MAX_RECURSION', 20);

$REPLACES = array();

define('TOKEN_ARRAY', "array: ");
define('TOKEN_OBJECT', "object: ");

function TplImport($filename, $replaces = array()) {
	return FileTemplateReplace($filename, $replaces);
}

function FileTemplate($filename, $replaces = array()) {
	if (!is_file($filename))
		return false;

	extract($GLOBALS);
	extract($replaces);
	
	ob_start();
	$value = include($filename);
	$contents = ob_get_contents();
	ob_end_clean();

	if (is_array($value)) {
		$replaces += $value;
	}

	return TemplateReplace($contents, $replaces);
}

// Replace values into a template file; return as string
function FileTemplateReplace($filename, $replaces = array()) {
	$template = get_include_contents($filename)
		or die("Could not open: $filename");
	return TemplateReplace($template, $replaces);
}

// Add all applicable globals, then perform templating
function TemplateReplace($template, $replaces = array()) {
	global $REPLACES;

	$replaces += $_SERVER + $GLOBALS + get_defined_constants();
	if (isset($_POST))
		$replaces += $_POST;
	if (isset($_GET))
		$replaces += $_GET;
	if (isset($_SESSION))
		$replaces += $_SESSION;
	if (isset($_COOKIE))
		$replaces += $_COOKIE;
	if (isset($REPLACES))
		$replaces += $REPLACES;

	return SimpleReplace($template, $replaces);
}

function SimpleReplace($template, $replaces = array()) {
	$replaces = GenerateReplaceArray(null, $replaces);

	$globalKeys = array_map("add_global_brackets", array_keys($replaces));
	$localKeys = array_map("add_local_brackets", array_keys($replaces));
	$strValues = array_values($replaces);
	//foreach ($strValues as $key => $value)
	//  $strValues[$key] = strval($value);

	$template = preg_replace('/\$\{([^\}]*)\}/', '\0|\0', $template);

	// Perform meta replacement within HTML code
	$ii = 0;
	do {
		do {
			$ii++;
			$oldpage = $template;
			// apply replacement until all replacement strings are done
			$template = str_replace($localKeys, array_values($replaces), $template);
		} while ($oldpage != $template && $ii <= MAX_RECURSION);
		
		$template = str_replace($globalKeys, array_values($replaces), $template);
	} while ($oldpage != $template && $ii <= MAX_RECURSION);
	
	if (isdebug())
		$template = preg_replace('/\$\{[^\}]*\}\|\$(\{[^\}]*\})/',
								 '\1', $template);
	else
		$template = preg_replace('/\$\{[^\}]*\}\|\$\{[^\}]*\}/', '', $template);
	$template = preg_replace('/\$\{[^\}]*\}/', '', $template);
	
	return $template;
}

// Turn arrays into keys like arr[key]; remove objects
function GenerateReplaceArray($prefix, $value) {
	if (is_object($value))
		return array($prefix => TOKEN_OBJECT . $prefix);
	if (!is_array($value)) {
		if (!empty($prefix) && (($loc = strrpos($prefix, '[')) !== false)) {
			$newval = preg_replace('/\$\{([^\}\[]*)([^\}]*)\}/', '${' . substr($prefix, 0, $loc) . '[\1]\2}|${\1\2}', $value);
		} else
			$newval = preg_replace('/\$\{([^\}]*)\}/', '\0|\0', $value);
		
		return array($prefix => $newval);
	}
	
	$replaces = array($prefix => TOKEN_ARRAY . $prefix);
	foreach ($value as $childkey => $childval)
		if (empty($prefix))
			$replaces += GenerateReplaceArray($childkey, $childval);
		else {
			$hasbrack = mb_strpos($childkey, '[');
			if ($hasbrack !== false)
				$childkey = $prefix . '[' . substr($childkey, 0, $hasbrack) . ']' . 
					substr($childkey, $hasbrack);
			else
				$childkey = $prefix . '[' . $childkey . ']';
			$replaces += GenerateReplaceArray($childkey, $childval);
		}

	return $replaces;
}

function SetReplaces($key, $value) {
	global $REPLACES;

	$REPLACES[$key] = $value;
}

function GetReplaces($key) {
	global $REPLACES;

	if (isset($REPLACES[$key])) {
		return $REPLACES[$key];
	} else {
		return null;
	}
}

function AddReplaces($key, $value) {
	$repl = GetReplaces($key);
	if (!is_null($repl)) {
		if (is_array($repl))
			$repl += $value;
		else
			$repl .= $value;
	} else
		$repl = $value;
	
	SetReplaces($key, $value);
}

function add_global_brackets($key) {
	return '|${' . $key . '}';
}

function add_local_brackets($key) {
	return '${' . $key . '}|';
}

// process a PHP form and return its contents as a string
function get_include_contents($filename) {
	extract($GLOBALS);
	
	if (is_file($filename)) {
		ob_start();
		include $filename;
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	return false;
}

?>