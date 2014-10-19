<?php

//require_once($toinc . "base.php");

function makeDefault($label, $id, $value = "", $desc = "") {
  return array('label' => $label,
	       'id' => $id,
	       'value' => $value,
	       'desc' => $desc);
}

function formDefault($ctrl, $form = array(), $message = "") {
  $value = value($ctrl['id'], $form, $ctrl['value']);
  return array('value' => $value,
	       'form' => $value);
}

function testDefault($ctrl, &$form) {
  if (isset($form[$ctrl['id']])) {
    if (is_scalar($form[$ctrl['id']])) {
      return true;
    } else {
      return errmsg(ERROR_INPUT);
    }
  } else {
    return false;
  }
}

function procDefault(&$ctrl, $form) {
  if (isset($form[$ctrl['id']])) {
    return array($ctrl['id'] => $form[$ctrl['id']]);
  } else {
    return array();
  }
}

function undbDefault(&$ctrl, $fields, $relations, $data) {
  $value = sqlGetValue($ctrl['id'], $fields, $relations, $data);
  $ctrl['value'] = $value;
}

function viewDefault($ctrl) {
  if (isset($ctrl['value']) && is_scalar($ctrl['value'])) {
    return array('view' => strval($ctrl['value']));
  } else {
    return array('view' => "");
  }
}

?>
