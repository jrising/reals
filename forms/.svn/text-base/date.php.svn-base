<?php

require_once("control.php");

function makeDate($label, $id, $value = null, $desc = "") {
  return makeControl('date', $label, $id, $value, $desc) +
    array('form' => 'formDate', 'test' => 'testDate',
	  'proc' => 'procDate', 'undb' => 'undbDate', 'view' => 'viewDate');
}

function formDate($date, $form = array(), $message = "") {
  $info = cal_info(CAL_GREGORIAN);
  $months = $info['months'];
  $days = range(0, $info['maxdaysinmonth']);
  unset($days[0]);
  $id = $date['id'];

  $result = formDefault($date, $form, $message);
  $time = $result[$id]['form'];
  if (is_null($time)) {
    $value = array('year' => "", 'month' => null, 'day' => null);
  } else if (is_array($time)) {
    $value = array('year' => $time['year'],
		   'month' => $time['month'],
		   'day' => $time['day']);
  } else {
    $dinfo = getdate($time);
    $value = array('year' => $dinfo['year'],
		   'month' => $dinfo['mon'],
		   'day' => $dinfo['mday']);
  }

  $html = iselect(options($months), "${id}[month]", $value['month']) . " " .
    iselect(options($days), "${id}[day]", $value['day']) . ", " .
    itext("${id}[year]", $value['year'], 4);

  return array('input' => $html, 'form' => $html . $message);
}

function testDate($date, &$form) {
  if (isset($form[$date['id']]['year']) &&
      isset($form[$date['id']]['month']) &&
      isset($form[$date['id']]['day'])) {
    $info = $form[$date['id']];
    if (empty($info['year'])) {
      return true;  // proc will set to null
    } else {
      if (checkdate($info['month'], $info['day'], $info['year'])) {
	return true;
      } else {
	return errmsg(ERROR_INVALID_DATE);
      }
    }
  } else {
    return false;
  }
}

function procDate(&$date, $form) {
  $info = $form[$date['id']];
  if (empty($info['year'])) {
    $date['value'] = null;
    return array($date['id'] => null);
  } else {
    $date['value'] = mktime(0, 0, 0, $info['month'],
			    $info['day'], $info['year']);
    return array($date['id'] => dbprep(date("Y-m-d", $date['value'])));
  }
}

function undbDate(&$date, $fields, $relations, $data) {
  undbDefault($date, $fields, $relations, $data);
  $date['value'] = strtotime($date['value']);
}

function viewDate($date) {
  if (is_null($date['value'])) {
    return t("No Date");
  } else {
    return d($date['value']);
  }
}

?>
