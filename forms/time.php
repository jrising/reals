<?php

require_once("control.php");
require_once("date.php");

define('ERROR_INVALID_TIME', "(invalid)");

function makeTime($label, $id, $value = null, $desc = "") {
  return makeControl('time', $label, $id, $value, $desc) +
    array('form' => 'formTime', 'test' => 'testTime',
	  'proc' => 'procTime', 'undb' => 'undbTime', 'view' => 'viewTime');
}

function formTime($time, $form = array(), $message = "") {
  $id = $time['id'];
  $result = formDefault($time, $form, $message);

  $moment = $result[$id]['form'];
  if (is_null($moment)) {
    $value = array('hour' => "", 'minute' => "");
  } else if (is_array($moment)) {
    $value = array('hour' => $moment['hour'],
		   'minute' => $moment['minute']);
  } else {
    $dinfo = getdate($moment);
    $value = array('hour' => $dinfo['hours'],
		   'minute' => $dinfo['minutes']);
  }

  $dayres = formDate($time, $form, $message);

  $html = $dayres['input'] . "&nbsp;&nbsp;" .
    itext("${id}[hour]", $value['hour'], 2) . ":" .
    itext("${id}[minute]", sprintf("%02d", $value['minute']), 2);

  return array('input' => $html, 'form' => $html . $message);
}

function testTime($time, &$form) {
  $result = testDate($time, $form);
  if ($result !== true) {
    return $result;
  }

  if (isset($form[$time['id']]['year']) &&
      isset($form[$time['id']]['hour']) &&
      isset($form[$time['id']]['minute'])) {
    $info = $form[$time['id']];
    if (empty($info['year'])) {
      return true;  // proc will set to null
    } else {
      if (is_numeric($info['hour']) && $info['hour'] >= 0 &&
	  $info['hour'] < 60 && is_numeric($info['minute']) &&
	  $info['minute'] >= 0 && $info['minute'] < 60) {
	return true;
      } else {
	return errmsg(ERROR_INVALID_TIME);
      }
    }
  } else {
    return false;
  }
}

function procTime(&$time, $form) {
  $result = procDate($time, $form); // update value
  if (is_null($time['value'])) {
    return $result;
  } else {
    $info = $form[$time['id']];
    $time['value'] = mktime($info['hour'], $info['minute'], 0,
			    $info['month'], $info['day'], $info['year']);
    return array($time['id'] => dbprep(date("Y-m-d H:i", $time['value'])));
  }
}

function undbTime(&$time, $fields, $relations, $data) {
  undbDefault($time, $fields, $relations, $data);
  $time['value'] = strtotime($time['value']);
}

function viewTime($time) {
  if (is_null($time['value'])) {
    return t("No Time");
  } else {
    return d($time['value']);
  }
}

?>
