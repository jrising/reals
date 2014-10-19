<?php

/*
 * all of these functions have the prototype:
 *   f($form, $id, ...) = {$id => error message}
 *   except formError, formSuccess, and formIgnore
 */ 

define('ERROR_REQUIRED', "required");

function formRequire($form, $id) {
  if (isset($form[$id]) && !empty($form[$id])) {
    return formSuccess();
  } else {
    return formError($id, ERROR_REQUIRED);
  }
}

function formError($id, $message) {
  return array($id => $message);
}

function formSuccess($id = null) {
  if (is_null($id)) {
    return array();
  } else {
    return array($id => true);
  }
}

function formIgnore($id) {
  return array($id => false);
}

function errmsg($text) {
  return inltag('font', array('color' => '#FF0000'), $text);
}

?>
