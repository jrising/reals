<?php

function value($id, $form, $value = null) {
	$newval = iget($form, $id);
	if (!is_null($newval)) {
		if (is_array($value) && is_array($newval))
			$newval = re_merge($value, $newval);

		return $newval;
	} else {
		return $value;
	}
}

// Array indexing, that allows for foo[bar] style indexes
function iget($arr, $id) {
	if (isset($arr[$id]))
		return $arr[$id];

	if (($endfirst = mb_strpos($id, '[')) !== false) {
		$first = substr($id, 0, $endfirst);
		if (isset($arr[$first])) {
			$endnext = mb_strpos($id, ']');
			$rest = substr($id, $endfirst + 1, $endnext - $endfirst - 1) .
				substr($id, $endnext + 1);
			return iget($arr[$first], $rest);
		}
	}

	return null;
}

function ivar() {
	$args = func_get_args();
	do {
		$var = array_shift($args);
	} while ($var == '');
	foreach ($args as $piece)
		$var .= "[$piece]";

	return $var;
}

/***** Structured Replacements *****/

function re_new($id, $repl) {
	return array($id => $repl);
}

// String replacements of renv2 replace those in renv1
function re_merge($renv1, $renv2) {
	$renv = $renv1;
	foreach ($renv2 as $id => $repl) {
		if (is_array($repl) && isset($renv[$id]) && is_array($renv[$id])) {
			$renv[$id] = re_merge($renv[$id], $repl);
		} else
			$renv[$id] = $repl;
	}

	return $renv;
}

function re_var() {
	$args = func_get_args();
	return '${' . call_user_func_array('ivar', $args) . '}';
}

?>