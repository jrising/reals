<?php

require_once("control.php");
require_once("multiple.php");

function makeListing($label, $id, $ctrls, $desc = "") {
  return makeControl('listing', $label, $id, null, $desc) +
    array('ctrls' => $ctrls, 'form' => 'formListing',
	  'test' => 'testListing', 'proc' => 'procListing',
	  'undb' => 'undbListing', 'view' => 'viewListing');
}

function formListing($listing, $form = array(), $message = "") {
  if (is_array($listing['value'])) {
    $repl = array();
    $formed = "";

    // save all ids
    $ids = array();
    foreach ($listing['ctrls'] as $cid => $ctrl)
      $ids[$cid] = $ctrl['id'];

    foreach ($listing['value'] as $unique => $values) {
      foreach ($values as $cid => $value)
	$listing['ctrls'][$cid]['value'] = $value;

      // replace ids
      $newids = array();
      foreach ($listing['ctrls'] as $cid => $ctrl) {
	$newids[$cid] = ivar($ids[$cid], $unique);
	$listing['ctrls'][$cid]['id'] = $newids[$cid];
      }

      //$multiple = makeMultiple($listing['ctrls'], $listing['id']) + $listing;
      $multiple = makeMultiple($listing['ctrls'], $unique) + $listing;
      if (isset($listing['rowformat'])) {
	$multiple['format'] = $listing['rowformat'];
	$multiple['format'] = str_replace($ids, $newids, $multiple['format']);
      }

      $repl += re_new($unique, formControl($multiple, $form, $message));
      $formed .= re_var($unique, '');
    }

    return array('input' => $formed, 
		 'form' => $message . $formed) + $repl;
  } else {
    return array('input' => '',
		 'form' => $message);
  }
}

function testListing($listing, &$post) {
  if (is_array($listing['value'])) {
    // save all full data
    $ids = array();
    foreach ($listing['ctrls'] as $cid => $ctrl)
      $ids[$cid] = $ctrl['id'];

    $result = array();
    foreach ($listing['value'] as $unique => $values) {
      foreach ($values as $cid => $value)
	$listing['ctrls'][$cid]['value'] = $value;

      // replace ids
      $newids = array();
      foreach ($listing['ctrls'] as $cid => $ctrl) {
	$newids[$cid] = ivar($ids[$cid], $unique);
	$listing['ctrls'][$cid]['id'] = $newids[$cid];
      }
    
      $result[$unique] = testControl(makeMultiple($listing['ctrls'], $listing['id']), $post);
    }

    return array($listing['id'] => $result);
  } else
    return testForm(makeForm($listing['ctrls']) + $listing, $post);
}

function procListing(&$listing, $post) {
  if (is_array($listing['value'])) {
    $result = array();
    foreach ($listing['value'] as $unique => $values) {
      foreach ($values as $cid => $value)
	$listing['ctrls'][$cid]['value'] = $value;
    
      $result[$unique] = procForm(makeForm($listing['ctrls']) + $listing, $post, array());
    }

    return array($listing['id'] => $result);
  } else
    return procForm(makeForm($listing['ctrls']) + $listing, $post, array());
}

function undbListing(&$listing, $fields, $relations, $data) {
  $id = $listing['id'];
  $alias = $fields[$id];
  list($table, $conds) = sqlGetConds($alias, $fields, $relations, $data);
  $where = sqlGenerateConditionSet($conds, " and ",
				   $fields, $relations, $data);

  $values = array();

  $result = dbquery("select $id from $table where $where");
  while ($result && $row = mysql_fetch_array($result, MYSQL_NUM)) {
    list($unique) = $row;
    $data[$alias][$id] = $unique;

    foreach ($listing['ctrls'] as $cid => $ctrl) {
      if (isset($fields[$ctrl['id']]) &&
	  !empty($fields[$ctrl['id']])) {
	undbControl($listing['ctrls'][$cid], $fields, $relations, $data);
	$values[$unique][$cid] = $listing['ctrls'][$cid]['value'];
      }
    }
  }

  $listing['value'] = $values;
}

function viewListing($listing) {
  return viewForm(makeForm($listing));
}

?>
