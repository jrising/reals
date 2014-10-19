<?php

// NEED TO UPDATE!

require_once("control.php");
require_once(comdir("html/form.php"));

function makeHidden($id, $value = "", $desc = "") {
  return makeControl('hidden', null, $id, $value, $desc) +
    array('form' => 'formHidden');
}

function formHidden($hidden, $form = array(), $message = "") {
  $value = formDefault($hidden, $form, $message);

  $input = ihidden($hidden['id'], $value['form']);

  return array('input' => $input, 'form' => $input);
}

// testHidden: default
// procHidden: default
// viewHidden: default

?>
