<?php

require_once("control.php");

function makeSubmitImage($label, $id, $value = "",
			 $imgsrc = "", $desc = null) {
  return makeControl('subimg', $label, $id, $value, $desc) +
    array('form' => 'formSubmitImage', 'src' => $imgsrc);
}

function formSubmitImage($submit, $form = array(), $message = "") {
  return array('form' => iimage($submit['id'], $submit['value'],
				$submit['src']),
	       'input' => iimage($submit['id'], $submit['value'],
				 $submit['src']));
}

function testSubmitImage($submit, &$form) {
  return false;
}

// procSubmitImage: default
// viewSubmitImage: default

?>
