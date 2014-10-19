<?php

require_once("control.php");

function makeHeadline($label, $id = null, $desc = null) {
  if (is_null($id))
    $id = $label;
  return makeControl('headline', null, $id, null, $desc) +
    array('text' => $label, 'form' => 'viewHeadline', 'view' => 'viewHeadline');
}

function viewHeadline($headline, $form = array(), $message = "") {
  return array('input' => center(h1($headline['text'])),
	       'form' => center(h1($headline['text'])));
}

// testHeadline: default
// procHeadline: default

?>
