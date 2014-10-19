<?php

require_once("control.php");
require_once("image.php");

define('ERROR_INVALID_IMGSELECT', "invalid");

function makeImageSelect($label, $id, $selectid = null, $thumbid = null,
			 $nameid = null, $location = null, $value = "",
			 $desc = "", $options = array()) {
  return makeControl('imageselect', $label, $id, "", $desc) +
    array('form' => 'formImageSelect', 'test' => 'testImageSelect',
	  'proc' => 'procImageSelect', 'undb' => 'undbImageSelect',
	  'view' => 'viewImageSelect',
	  'selectid' => $selectid, 'selectvalue' => $value,
	  'thumbid' => $thumbid, 'nameid' => $nameid,
	  'locn' => $location, 'options' => $options);
}

function formImageSelect($image, $form = array(), $message = "") {
  $value = formImage($image, $form, $message);
  $selectvalue = value($image['selectid'], $form, $image['selectvalue']);

  $id = $image['id'];
  $namefield = " Title: " . itext($image['nameid'], $form[$image['nameid']], 20);
  $selectbox = " Select: " . iselect(options($image['options']), $image['selectid'], $selectvalue);

  return array('input' => $value['input'] . br() . $namefield . $selectbox,
	       'form' => $value['form'] . br() . $namefield, $selectbox);
}

function testImageSelect($image, &$form) {
  $message = testImage($image, $form);
  if (!is_string($message)) {
    if (isset($image['options'][$form[$image['selectid']]])) {
      if (!isset($form[$image['id']]))
	$form[$image['id']] = ''; // so evals proc
      return true;
    } else if (!isset($form[$image['selectid']])) {
      return true;
    } else {
      return errmsg(ERROR_INVALID_IMGSELECT);
    }
  }

  return $message;
}

function procImageSelect(&$image, $form) {
  $changes = procImage($image, $form);

  // add new image to list
  if (!empty($changes[$image['id']])) {
    $key = $changes[$image['id']];
    if (!empty($form[$image['nameid']]))
      $name = $form[$image['nameid']];
    else
      $name = basename($_FILES[$image['id']]['name']);

    $image['options'][$key] = $name;
    $image['selectvalue'] = $key;
    $changes[$image['selectid']] = $key;
    $changes[$image['nameid']] = $name;
  } else {
    // update selectid value
    if (isset($form[$image['selectid']])) {
      if ($form[$image['selectid']] != $image['selectvalue']) {
	$changes[$image['selectid']] = $form[$image['selectid']];
      }
    }
  }

  return $changes;
}

function undbImageSelect(&$image, $fields, $relations, $data) {
  $value = sqlGetValue($image['selectid'], $fields, $relations, $data);
  $image['selectvalue'] = $value;
}

function viewImageSelect($image) {
  if (is_null($image['selectvalue'])) {
    return "None";
  } else {
    return img($image['selectvalue']);
  }
}

?>
