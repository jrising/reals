<?php

/*
 * Encode all post and get information into the form for submission
 */

setup(__FILE__, 'share');

require_once("control.php");
require_once(exinc("form.php"));

function makeEnviron($id = '_env_') {
	return makeControl('environ', "", $id, array('post' => $_POST,
												 'get' => $_GET), "") +
		array('form' => 'formEnviron', 'test' => 'testEnviron',
			  'format' => '[*.form]');
}

function formEnviron($environ, $form = array(), $message = "") {
	$value = formDefault($environ, $form, $message);

	return MakeAllHidden($environ['id'], $value);
}

function MakeAllHidden($prefix, $value) {
	if (is_array($value)) {
		$result = "";
		foreach ($value as $key => $subval) {
			$result .= MakeAllHidden($prefix . "[$key]", $subval);
		}
		return $result;
	} else {
		return hidden($prefix, $value);
	}
}

function testEnviron($environ, &$form) {
	// note: this doesn't generall effect the current processing
	if (isset($_POST[$environ['id']]['post'])) {
		$_POST += $_POST[$environ['id']]['post'];
	}
	if (isset($_POST[$environ['id']]['get'])) {
		$_GET += $_POST[$environ['id']]['get'];
	}

	return false; // no more processing!
}

// procEnviron: default
// viewEnviron: default

final();

?>
