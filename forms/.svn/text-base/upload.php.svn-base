<?php

require_once("control.php");

function makeUpload($label, $id, $value = "", $desc = "") {
  return makeControl('upload', $label, $id, $value, $desc) +
    array('form' => 'formUpload', 'test' => 'testUpload',
	  'view' => 'viewUpload');
}

function formUpload($upload, $form = array(), $message = "") {
  $result = formDefault($upload, $form, $message);

  $upload = upload($upload['id'], $result[$upload['id']], 1000000);

  return array('input' => $upload,
	       'form' => $upload . ' ' . $message);
}

function testUpload($upload, &$form) {
  if (isset($_FILES[$upload['id']]) && $_FILES[$upload['id']]) {
    if (is_uploaded_file($_FILES[$upload['id']]['tmp_name'])) {
      $form[$upload['id']] = $_FILES[$upload['id']]['tmp_name'];
      return true;
    } else if (isset($_FILES['file']) && !empty($_FILES['file']['name'])) {
      return errmsg(ERROR_INVALID_FILE);
    }
  } else {
    return false;
  }
}

function viewUpload($upload) {
  if (is_null($upload['value'])) {
    return "None";
  } else {
    return "Uploaded";
  }
}

?>
