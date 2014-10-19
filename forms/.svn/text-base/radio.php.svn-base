<?php

// NEED TO UPDATE!

require_once("control.php");

function makeRadio($label, $id, $value = "", $desc = "", $options = array()) {
  return makeControl('radio', $label, $id, $value, $desc) +
    array('options' => $options, 'form' => 'formRadio',
	  'test' => 'testRadio', 'view' => 'viewRadio');
}

function formRadio($radio, $form = array(), $message = "") {
  $value = formDefault($radio, $form, $message);

  $id = $radio['id'];
  $optionset = "";
  foreach ($radio['options'] as $val => $name) {
    if ($value == $val) {
      $optionset .= "<input type=\"radio\" name=\"$id\" id=\"$id\" value=\"$val\" checked=\"checked\">$name<br />\n";
    } else {
      $optionset .= "<input type=\"radio\" name=\"$id\" id=\"$id\" value=\"$val\">$name<br />\n";
    }
  }

  return $optionset . $message;
}

function testRadio($radio, &$form) {
  $message = formDefault($radio, $form);
  if ($message === true) {
    if (isset($radio['options'][$form[$radio['id']]])) {
      return true;
    } else {
      return errmsg(ERROR_INVALID_RADIO);
    }
  }
}

// procRadio: default

function viewRadio($radio) {
  if (is_null($radio['value'])) {
    return "No selection";
  } else {
    return $radio['options'][$radio['value']];
  }
}

?>
