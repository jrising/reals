<?php

require_once("simple.php");

function itext($name, $value = null, $size = 20) {
	$value = value($name, $value);
	return soltag('input', array('type' => 'text', 'name' => $name,
								 'value' => $value, 'size' => $size));
}

function ihidden($name, $value = null) {
	$value = value($name, $value);
	return soltag('input', array('type' => 'hidden', 'name' => $name,
								 'value' => $value));
}

function ipassword($name, $size = 10) {
	$value = value($name, $value);
	return soltag('input', array('type' => 'password', 'name' => $name,
								 'size' => $size));
}

function itextarea($name, $value = null, $rows = 4, $cols = 60) {
	$value = value($name, $value);
	return inltag('textarea', array('name' => $name,
									'rows' => $rows, 'cols' => $cols), $value);
}

function iradio($name, $value = null, $checked = false) {
	$value = value($name, $value);
	if ($checked)
		return soltag('input', array('type' => 'radio', 'name' => $name,
									 'value' => $value,
									 'checked' => 'checked'));
	else
		return soltag('input', array('type' => 'radio', 'name' => $name,
									 'value' => $value));
}

function icheckbox($name, $value = null, $checked = false) {
	$value = value($name, $value);
	if ($checked)
		return soltag('input', array('type' => 'checkbox', 'name' => $name,
									 'value' => $value,
									 'checked' => 'checked'));
	else
		return soltag('input', array('type' => 'checkbox', 'name' => $name,
									 'value' => $value));
}

function iselect($options, $name, $value = null, $attr = array()) {
	$value = value($name, $value);
	$options = preg_replace('@(value=[\'"]' . $value . '[\'"])@',
							'\1 selected="selected"', $options);

	return enctag('select', $attr + array('name' => $name), $options);
}

function ioption($value, $title) {
	$value = value($name, $value);
	return inltag('option', array('value' => $value), $title);
}

function ioptions($arr) {
	$value = value($name, $value);
	$options = "";
	foreach ($arr as $value => $title) {
		$options .= ioption($value, $title);
	}

	return $options;
}

function isubmit($name, $value = null) {
	$value = value($name, $value);
	return soltag('input', array('name' => $name, 'type' => 'submit', 'value' => $value));
}

function iimage($name, $value = null, $src = null) {
	$value = value($name, $value);
	return soltag('input', array('type' => 'image', 'name' => $name, 'value' => $value, 'src' => $src));
}

function form($contents, $action = null, $method = 'post') {
	$value = value($name, $value);
	$attr = array('method' => $method, 'enctype' => "multipart/form-data");

	return enctag('form', array('action' => $action) + $attr, $contents); 
}

function uupload($name, $value = null, $size = 30000) {
	$value = value($name, $value);
	return ihidden('MAX_FILE_SIZE', $size) .
		soltag('input', array('type' => 'file', 'name' => $name,
							  'id' => $name, 'class' => 'file',
							  'value' => $value);
}


?>