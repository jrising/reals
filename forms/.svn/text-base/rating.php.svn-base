<?php

// NEED TO UPDATE!

require_once("control.php");

function makeRating($label, $id, $value = null, $desc = "") {
  return makeControl('rating', $label, $id, $value, $desc) +
    array('form' => 'formRating', 'test' => 'testRating',
	  'view' => 'viewRating');
}

function formRating($rating, $form = array(), $message = "") {
  $value = formDefault($rating, $form, $message);
  $id = $rating['id'];

  return table(tr(td(radio($id, "1", $value < 1.8 ? "checked=\"checked\"" : "") .
		     radio($id, "2", ($value >= 1.8 && $value < 2.6) ? "checked=\"checked\"" : "") .
		     radio($id, "3", ($value >= 2.6 && $value < 3.4) ? "checked=\"checked\"" : "") .
		     radio($id, "4", ($value >= 3.4 && $value < 4.2) ? "checked=\"checked\"" : "") .
		     radio($id, "5", ($value >= 4.2) ? "checked=\"checked\"" : ""))) .
	       tr(td(t("poor") . t(" - ") . t("great")))) . $message;
}

function testRating($rating, &$form) {
  if (isset($form[$rating['id']])) {
    if (is_numeric($form[$rating['id']]) &&
	$form[$rating['id']] >= 1 && $form[$rating['id']] <= 5) {
      return true;
    } else {
      return errmsg(ERROR_INPUT);
    }
  } else {
    return false;
  }
}

// procRating: default

function viewRating($rating) {
  if (is_null($rating['value'])) {
    return t("Unrated");
  } else {
    return rating($rating['value']);
  }
}

?>
