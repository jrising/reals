<?php

require_once("control.php");

function makePassword($label, $id, $value = "", $desc = "", $minlen = 1, $size = 16) {
  return makeControl('password', $label, $id, $value, $desc) +
    array('minlen' => $minlen, 'size' => $size,
	  'form' => 'formPassword', 'test' => 'testPassword',
	  'proc' => 'procPassword', 'view' => 'viewPassword');
}

function formPassword($passwd, $form = array(), $message = "") {
  $id = $passwd['id'];
  $size = $passwd['size'];

  $input = ipassword($passwd['id'], $passwd['size']);

  return array('input' => $input,
	       'form' => $input . $message);
}

function testPassword($passwd, &$form) {
  $message = testDefault($passwd, $form);
  if ($message === true) {
    if (mb_strlen($form[$passwd['id']]) >= $passwd['minlen']) {
      return true;
    } else {
      return errmsg("Password too short");
    }
  } else {
    return $message;
  }
}

function procPassword(&$ctrl, $form) {
  $ctrl['value'] = ($form[$ctrl['id']] != "");
  return array($ctrl['id'] => array(dbprep("PASSWORD(%s)", $form[$ctrl['id']])));
}

function viewPassword($ctrl) {
  if ($ctrl['value']) {
    return t("Password Set");
  } else {
    return t("No Password");
  }
}

?>
