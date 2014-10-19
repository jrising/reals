<?php

require_once("control.php");
require_once(comdir("html/form.php"));

function makeTextarea($label, $id, $value = "", $desc = null,
		      $rows = 4, $cols = 40) {
  return makeControl('textarea', $label, $id, $value, $desc) +
    array('rows' => $rows, 'cols' => $cols,
	  'form' => 'formTextarea', 'view' => 'viewTextarea');
}

function formTextarea($area, $form = array(), $message = "") {
  $value = formDefault($area, $form, $message);

  $input = itextarea($area['id'], $value['form'],
		     $area['rows'], $area['cols']);
  return array('input' => $input,
	       'form' => $input . $message);
}

// testTextarea: default
// procTextarea: default

function viewTextarea($area) {
  $value = $area['value'];
  $width = $area['rows'] * 6;
  return "<table><tr><td width=\"$width\">$value</td></tr></table>";
}

?>
