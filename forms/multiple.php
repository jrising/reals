<?php

require_once("control.php");

function makeMultiple($ctrls, $id, $value = false) {
  return makeControl('multiple', null, $id, $value, null) +
    array('ctrls' => $ctrls, 'form' => 'formMultiple',
	  'test' => 'testMultiple', 'proc' => 'procMultiple',
	  'undb' => 'undbMultiple', 'view' => 'viewMultiple');
}

function formMultiple($mult, $post = array(), $msgs = array()) {
  $replaces = array();

  $block = "";
  foreach ($mult['ctrls'] as $cid => $ctrl) {
    $msg = isset($msgs[$ctrl['id']]) ? $msgs[$ctrl['id']] : "";

    if (is_control($ctrl)) {
      $replaces += re_new($ctrl['id'], formControl($ctrl, $post, $msg, $mult));
      $block .= re_var($ctrl['id'], '');
    } else if (is_scalar($ctrl)) {
      $block .= $ctrl;
    } else {
      // what to do?
    }
  }    

  if (!isset($mult['format'])) {
    $replaces[''] = $block;
  }
  $replaces['input'] = $block;

  return $replaces;
}

function testMultiple($mult, &$post) {
  $msgs = array();
  foreach ($mult['ctrls'] as $cid => $ctrl) {
    $result = testControl($ctrl, $post);
    if (is_array($result))
      $msgs += $result;
    else
      $msgs += array($ctrl['id'] => $result);
  }

  return re_new($mult['id'], $msgs);
}

function procMultiple(&$mult, $post, $msgs = true) {
  $changes = array();
  foreach ($mult['ctrls'] as $cid => $ctrl) {
    if ($msgs === true ||
	(isset($msgs[$ctrl['id']]) && $msgs[$ctrl['id']] === true)) {
      $changes += procControl($ctrl, $post);
      $mult['ctrls'][$cid] = $ctrl;
    }
  }

  return $changes;

}

function undbMultiple(&$mult, $fields, $relations, $data) {
  $msgs = array();

  foreach ($mult['ctrls'] as $cid => $ctrl) {
    $result = undbControl($ctrl, $fields, $relations, $data);
    $mult['ctrls'][$cid] = $ctrl;
    if (is_array($result))
      $msgs += $result;
    else
      $msgs += array($ctrl['id'] => $result);
  }

  return re_new($mult['id'], $msgs);
}

function viewMultiple($mult) {
  $replaces = array();

  $block = "";
  foreach ($mult['ctrls'] as $cid => $ctrl) {
    if (is_control($ctrl)) {
      $replaces += viewControl($ctrl);
      $block .= re_var($ctrl['id'], '');
    } else if (is_scalar($ctrl)) {
      $block .= $ctrl;
    } else {
      // what to do?
    }
  }

  $replaces['block'] = $block;

  return $replaces;
}

?>
