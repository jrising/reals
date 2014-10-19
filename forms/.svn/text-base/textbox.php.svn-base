<?php

require_once("control.php");

function makeTextbox($label, $id, $value = "", $desc = "", $size = 40) {
  return makeControl('textbox', $label, $id, $value, $desc) +
    array('size' => $size, 'form' => 'formTextbox');
}

function formTextbox($textbox, $form = array(), $message = "") {
  $value = formDefault($textbox, $form, $message);

  $input = itext($textbox['id'], $value['form'], $textbox['size']);
  return array('input' => $input, 
	       'form' => $input . $message);
}

// testTextbox: default
// procTextbox: default
// viewTextbox: default

?>
