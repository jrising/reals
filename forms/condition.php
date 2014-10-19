<?php

require_once("control.php");

function makeConditional($ctrls, $id, $value = false) {
  return makeControl('condition', null, $id, $value, null) +
    array('ctrls' => $ctrls, 'form' => 'formConditional',
	  'test' => 'testConditional', 'undb' => 'undbConditional');
}

function formConditional($cond, $form = array(), $message = "") {
  $value = value($cond['id'], $form, $cond['value']);

  if ($value) {
    $multiple = makeMultiple($cond['ctrls'], $cond['id']) + $cond;
    $repl = formControl($multiple, $form, $message);
    return re_merge(array('value' => $value), $repl);
  } else {
    return array('value' => $value,
		 'input' => '');
  }
}

function testConditional($cond, &$post) {
  $value = value($cond['id'], $post, $cond['value']);

  if ($value) {
    $multiple = makeMultiple($cond['ctrls'], $cond['id']) + $cond;
    $result = testControl($multiple, $post);
    return $result;
  } else {
    return array();
  }
}

function undbConditional(&$cond, $fields, $relations, $data) {
  $mult = makeMultiple($cond['ctrls'], $cond['id']) + $cond;
  $msgs = undbControl($mult, $fields, $relations, $data);
  $cond['ctrls'] = $mult['ctrls'];
  return $msgs;
}

// testConditional: default
// procConditional: default
// viewConditional: default

?>
