<?php

require_once("control.php");

function makeSelect($label, $id, $value = "", $desc = "", $options = array()) {
  return makeControl('select', $label, $id, $value, $desc) +
    array('options' => $options, 'form' => 'formSelect',
	  'test' => 'testSelect', 'view' => 'viewSelect');
}

function formSelect($select, $form = array(), $message = "") {
  $value = formDefault($select, $form, $message);

  $options = options($select['options']);
  $id = $select['id'];

  $selform = iselect($options, $id, $value['form']);
  return array('input' => $selform,
	       'form' => $selform . $message);
}

function testSelect($select, &$form) {
  $message = testDefault($select, $form);
  if ($message === true) {
    if (isset($select['options'][$form[$select['id']]])) {
      return true;
    } else {
      return errmsg(ERROR_INVALID_SELECT);
    }
  }

  return $message;
}

// procSelect: default

function viewSelect($select) {
  if (is_null($select['value'])) {
    return "No selection";
  } else {
    return $select['options'][$select['value']];
  }
}

?>
