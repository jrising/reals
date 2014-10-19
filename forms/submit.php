<?php

require_once("control.php");

function makeSubmit($label, $id, $value = "", $desc = null) {
  return makeControl('submit', $label, $id, $value, $desc) +
    array('form' => 'formSubmit', 'test' => 'testSubmit',
	  'undb' => 'undbSubmit');
}

function formSubmit($submit, $form = array(), $message = "") {
  return array('form' => isubmit($submit['id'], $submit['value']),
	       'input' => isubmit($submit['id'], $submit['value']));
}

function testSubmit($submit, &$form) {
  return false;
}

function undbSubmit(&$submit, $fields, $relations, $data) {
  return array();
}

// procSubmit: default
// viewSubmit: default

?>
