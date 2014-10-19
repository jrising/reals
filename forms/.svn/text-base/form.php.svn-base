<?php

require_once("dbdos.php");
require_once("control.php");
require_once(comdir("html/templates.php"));
require_once(comdir("mech/http.php"));

/*
 * Options:
 *   labelwidth: width of the label column (default: 150px)
 */

function makeForm($controls, $action = null, $fields = array(),
		  $relations = array()) {
  return array('controls' => $controls, 'action' => $action,
	       'fields' => $fields, 'relations' => $relations);
}

function formForm($form, $post = array(), $msgs = array()) {
  $mult = makeMultiple($form['controls'], '_');
  $replaces = formMultiple($mult, $post, $msgs);

  $replaces['block'] = "<table cellpadding='0' cellspacing='0' border='0'>\n" . $replaces['input'] . "\n</table>\n";

  if (isset($form['format']))
    $content = $form['format'];
  else
    $content = re_var('block');

  $content = SimpleReplace($content, $replaces);

  return form($content, $form['action']);
}

function testForm($form, &$post) {
  $mult = makeMultiple($form['controls'], '');
  $msgs = testMultiple($mult, $post);
  $msgs = $msgs['']; // get out messages as top-level

  if (isset($form['test'])) {
    $msgs = array_merge($msgs, $form['test']($form, $post));
  }

  return $msgs;
}

function isPassedTest($msgs) {
  foreach ($msgs as $message) {
    if (is_string($message)) {
      return false;
    }
    if (is_array($message) && !isPassedTest($message)) {
      return false;
    }
  }

  return true;
}

function handForm(&$form, $post, $msgs, $isInsert, &$data) {
  $mult = makeMultiple($form['controls'], '');
  $changes = procMultiple($mult, $post, $msgs);
  $form['controls'] = $mult['ctrls'];

  if (isset($form['proc']))
    $changes += $form['proc']($form, $post, $msgs, $isInsert, $data, $changes);

  if (!is_null($isInsert)) {
    $tblchanges = array();
    foreach ($changes as $id => $val) {
      $table = $form['fields'][$id];
      if ($table !== true) {
	if (isset($tblchanges[$table])) {
	  $tblchanges[$table][$id] = $val;
	} else {
	  $tblchanges[$table] = array($id => $val);
	}
      }
    }
  }

  if (is_bool($isInsert)) {
    // First do simple inserts
    foreach ($tblchanges as $alias => $changes) {
      if ($alias == '' || is_int($alias)) {
	unset($tblchanges[$alias]);
	continue;
      }
      if (rel_is_simple($alias, $form['relations'])) {
	if ($isInsert === true)
	  sqlInsertTable($alias, $changes, $form['fields'],
			 $form['relations'], $data);
	else
	  sqlUpdateTable($alias, $changes, $form['fields'],
			 $form['relations'], $data);
      }
    }
    // Next do joins
    foreach ($tblchanges as $alias => $changes)
      if (rel_is_join($alias, $form['relations'])) {
	$type = rel_get_joined_type($alias, $form['relations'], $isInsert);
	if ($type === true)
	  sqlInsertTable($alias, $changes, $form['fields'],
			 $form['relations'], $data);
	else if ($type === false)
	  sqlUpdateTable($alias, $changes, $form['fields'],
			 $form['relations'], $data);
      }
    // Finally, do updates
    foreach ($tblchanges as $alias => $changes)
      if (rel_is_update($alias, $form['relations'])) {
	sqlUpdateTable($form['relations'][$alias], $changes, $form['fields'],
		       $form['relations'], $data);
      }
  }

  return true;
}

function viewForm($form) {
  $mult = makeMultiple($form['controls'], '_');
  $replaces = formMultiple($mult, $post, $msgs);

  $replaces['block'] = "<table cellpadding='0' cellspacing='0' border='0'>\n" . $replaces['input'] . "\n</table>\n";

  if (isset($form['format']))
    $content = $form['format'];
  else
    $content = re_var('block');

  return SimpleReplace($content, $replaces);
}

// Eventually syntesize a single query from this info
// filter is array(col => value), to be used to create relation
function fillForm(&$form, $data) {
  $mult = makeMultiple($form['controls'], '');
  $msgs = undbMultiple($mult, $form['fields'], $form['relations'], $data);
  $form['controls'] = $mult['ctrls'];
  $msgs = $msgs['']; // get out msgs as top-level

  if (isPassedTest($msgs) && isset($form['undb']))
    $msgs += $form['undb']($form, $data);

  return $msgs;
}

function insertForm(&$form, &$post, &$data) {
  if (isPassedTest($msgs = testForm($form, $post))) {
    return handForm($form, $post, $msgs, true, $data);
  } else {
    return formForm($form, $post, $msgs);
  }
}

function updateForm(&$form, &$post, &$data) {
  if (isPassedTest($msgs = testForm($form, $post))) {
    return handForm($form, $post, $msgs, false, $data);
  } else {
    return formForm($form, $post, $msgs);
  }
}

function doprocForm(&$form, &$post, &$data) {
  if (isPassedTest($msgs = testForm($form, $post))) {
    return handForm($form, $post, $msgs, null, $data);
  } else {
    return formForm($form, $post, $msgs);
  }
}

// Utility functions

function findControl($form, $id) {
  foreach ($form['controls'] as $cid => $ctrl) {
    if ($ctrl['id'] == $id) {
      return $ctrl;
    }
  }

  return null;
}

function getAutoIncrement($table) {
  // Figure out which field was autoincremented, if any
  $result = dbquery("show columns from $table");
  while ($result && $row = dbfetch($result, DB_ASSOC)) {
    if (mb_strpos($row['Extra'], "auto_increment") !== false) {
      return $row['Field'];
    }
  }

  return null;
}

function isSubmitted() {
  if (count($_POST) > 0) {
    return $_POST;
  }

  return false;
}

?>
