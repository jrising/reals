<?php

require_once("control.php");

define('ERROR_FILE_INVALID', 'invalid file');
define('ERROR_FILE_TOOBIG', 'too large');

$kImageExtensions = array(IMAGETYPE_GIF => 'gif',
			  IMAGETYPE_JPEG => 'jpeg',
			  IMAGETYPE_PNG => 'png');

function makeImage($label, $id, $thumbid = null, $location = null, $value = "", $desc = "") {
  return makeControl('image', $label, $id, $value, $desc) +
    array('form' => 'formImage', 'test' => 'testImage',
	  'proc' => 'procImage', 'view' => 'viewImage',
	  'thumbid' => $thumbid, 'locn' => $location);
}

function formImage($image, $form = array(), $message = "") {
  $value = formDefault($image, $form, $message);

  $upload = upload($image['id'], $value, 3000000);
  if (empty($value['form']))
    $thumbnail = '';
  else if (!is_null($image['thumbid'])) {
    $fullname = $value['form'];
    $thumbname = str_replace('.', '_tn.', $fullname);
    if (!is_null($image['locn']))
      $thumbname = $image['locn'] . $thumbname;
    $thumbnail = br() . img(srvwww($thumbname));
  }

  $rotation = "Rotate " . icheckbox('rright') . " right " .
    icheckbox('rleft') . " left";

  return array('input' => $upload . $rotation . $thumbnail,
	       'form' => $upload . $rotation . $message . $thumbnail);
}

function testImage($image, &$form) {
  if (isset($_FILES[$image['id']]) && $_FILES[$image['id']]) {
    if (is_uploaded_file($_FILES[$image['id']]['tmp_name'])) {
      $form[$image['id']] = $_FILES[$image['id']]['tmp_name'];
      return true;
    } else if (isset($_FILES[$image['id']]) &&
	       !empty($_FILES[$image['id']]['name'])) {
      if ($_FILES[$image['id']]['error'] == UPLOAD_ERR_INI_SIZE ||
	  $_FILES[$image['id']]['error'] == UPLOAD_ERR_FORM_SIZE)
	return errmsg(ERROR_FILE_TOOBIG);
      return errmsg(ERROR_FILE_INVALID);
    }
  } else {
    return false;
  }
}

function procImage(&$image, $form) {
  global $kImageExtensions;

  if (!is_null($image['locn']) && !empty($form[$image['id']])) {
    // determine image type and attributes
    $tmpname = $form[$image['id']];
    list($width, $height, $type, $attr) = getimagesize($tmpname);
    // get an extension
    if ($type) {
      if (isset($kImageExtensions[$type]))
	$extension = '.' . $kImageExtensions[$type];
      else
	$extension = '';
    } else
      $extension = '';
    
    $newbase = tempnam($image['locn'], '');
    $newfile = $newbase . $extension;
    $newthumb = $newbase . '_tn' . $extension;

    if (isset($form['rright']) && $form['rright'] &&	
	isset($form['rleft']) && $form['rleft'])
      $options = "-rotate 180";
    if (isset($form['rright']) && $form['rright'])
      $options = "-rotate 90";
    else if (isset($form['rleft']) && $form['rleft'])
      $options = "";

    if ($width > $height) {
      exec("convert $tmpname -geometry 600x450 $options $newfile");
      if (!is_null($image['thumbid']))
	exec("convert $tmpname -geometry 120x90 $options $newthumb");
    } else {
      exec("convert $tmpname -geometry 450x600 $options $newfile");
      if (!is_null($image['thumbid']))
	exec("convert $tmpname -geometry 90x120 $options $newthumb");
    }

    $form[$image['id']] = basename($newfile);
    $result = array($image['id'] => basename($newfile));
    if (!is_null($image['thumbid']))
      $result[$image['thumbid']] = basename($newthumb);

    return $result;
  }

  return procDefault($image, $form);
}

function viewImage($image) {
  if (is_null($image['value'])) {
    return "None";
  } else {
    return img($image['value']);
  }
}

?>
