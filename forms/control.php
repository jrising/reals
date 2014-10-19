<?php

require_once("default.php");
require_once("templates.php");
require_once(comdir("utils.php"));
require_once(comdir("html/form.php"));
require_once(comdir("html/text.php"));

function makeControl($class, $label, $id, $value = "", $desc = null) {
  return array('class' => $class) + makeDefault($label, $id, $value, $desc);
}

function formControl($ctrl, $form = array(), $message = "",
		     $options = array()) {
  $label = st('editlabel', $ctrl['label'], aget($options, 'label'));
  $desc = st('editdesc', $ctrl['desc'], aget($options, 'desc'));
  $msg = st('editmessage', $message);
  $full = getFormat($ctrl);

  $replaces = array('label' => $label,
		    'desc' => $desc,
		    'msg' => $msg,
		    '' => $full);

  if (!isset($ctrl['form'])) {
    $replaces = re_merge($replaces,
			 formDefault($ctrl, $form, $message));
  } else {
    $replaces = re_merge($replaces,
			 $ctrl['form']($ctrl, $form, $message));
  }

  if (!isset($replaces['input']) && isset($replaces['view']))
    $replaces['input'] = $replaces['view'];

  return $replaces;
}

// returns message
function testControl($ctrl, &$form) {
  if (!isset($ctrl['test'])) {
    return testDefault($ctrl, $form);
  }

  return $ctrl['test']($ctrl, $form);
}

// returns array(id => form)
function procControl(&$ctrl, $form) {
  if (isset($form[$ctrl['id']])) {
    $ctrl['value'] = $form[$ctrl['id']];

    if (!isset($ctrl['proc'])) {
      return procDefault($ctrl, $form);
    } else {
      return $ctrl['proc']($ctrl, $form);
    }
  } else {
    return array();
  }
}

// take a value when read in from the database; returns nothing
function undbControl(&$ctrl, $fields, $relations, $data) {
  if (!isset($ctrl['undb'])) {
    undbDefault($ctrl, $fields, $relations, $data);
  } else {
    $ctrl['undb']($ctrl, $fields, $relations, $data);
  }
}

function viewControl($ctrl, $message = "", $options = array()) {
  $label = st('editlabel', $ctrl['label'], aget($options, 'label'));
  $desc = st('editdesc', $ctrl['desc'], aget($options, 'desc'));
  $msg = st('editmessage', $message);
  $full = getFormat($ctrl);

  $replaces = array('label' => $label,
		    'desc' => $desc,
		    'msg' => $msg,
		    'input' => '${' . $ctrl['id'] . '[view]}',
		    '' => $full);

  if (!isset($ctrl['view'])) {
    $replaces = re_merge($replaces, viewDefault($ctrl));
  } else {
    $replaces = re_merge($replaces, $ctrl['view']($ctrl));
  }

  return $replaces;
}

function is_control($obj) {
  return is_array($obj) && isset($obj['class']);
}

?>
