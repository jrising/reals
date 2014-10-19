<?php

/*
 * Functions which interact with the database
 * Intented as a replacement for direct sql commands
 */

// Look up a value for a field
function sqlGetValue($id, $fields, $relations, &$data) {
  if (is_array($fields[$id])) {
    $results = array();
    foreach ($fields[$id] as $key => $func) {
      $results[$key] = $func($id, $fields, $relations, $data, $results);
    }
    return $results['return'];
  } else if (isset($relations[$fields[$id]])) {
    $table = sqlGetTable($fields[$id], $fields, $relations, $data);
    return $table[sqlColumn($id)];
  } else {
    return $fields[$id];
  }
}

// Get an array representing the values in a table
function sqlGetTable($alias, $fields, $relations, &$data) {
  if (isset($data[$alias]) && isset($data[$alias]['#time#'])) {
    return $data[$alias];
  }

  list($table, $conds) = sqlGetConds($alias, $fields, $relations, $data);

  $where = sqlGenerateConditionSet($conds, " and ", $fields, $relations, $data);

  $result = dbquery("select * from $table where $where");
  if ($result && $row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    mysql_free_result($result);
    $data[$table] = $row + array('#time#' => time());
    return $data[$table];
  }

  return array();
}

function sqlInsertTable($alias, $changes, $fields, $relations, &$data) {
  // Analyze relations here
  list($table, $conds) = sqlGetConds($alias, $fields, $relations, $data);
  $changes += $conds;

  if ($autoinc = getAutoIncrement($table)) {
    if (isset($changes[$autoinc])) {
      // got index, ignore insert
      unset($data[$alias]);
      $data[$alias] = array($autoinc => $changes[$autoinc]);
      sqlGetTable($alias, $fields, $relations, $data);
      return;
    }
  }

  list($sqlfields, $sqlvalues) = sqlGenerateInsertSet($changes, $fields,
						      $relations, $data);

  dbquery("insert into $table ($sqlfields) values ($sqlvalues)");

  // Figure out which field was autoincremented, if any
  if ($autoinc = getAutoIncrement($table)) {
    unset($data[$alias]);
    $data[$alias] = array($autoinc => mysql_insert_id());
    sqlGetTable($alias, $fields, $relations, $data);
  }
}

function sqlUpdateTable($alias, $changes, $fields, $relations, &$data) {
  list($table, $conds) = sqlGetConds($alias, $fields, $relations, $data);

  if ($autoinc = getAutoIncrement($table)) {
    if (isset($changes[$autoinc])) {
      // got index, ignore insert
      unset($data[$alias]);
      $data[$alias] = array($autoinc => $changes[$autoinc]);
      sqlGetTable($alias, $fields, $relations, $data);
      return;
    }

    if (isset($conds[$autoinc])) {
      // this is only that matters
      $aivalue = $conds[$autoinc];
      unset($conds[$autoinc]);
      $changes += $conds;
      $conds = array($autoinc => $aivalue);
    }
  }

  if (!empty($conds)) {
    $updates = sqlGenerateConditionSet($changes, ", ", $fields, $relations, $data);
    $where = sqlGenerateConditionSet($conds, " and ", $fields, $relations, $data);

    dbquery("update $table set $updates where $where");

    unset($data[$alias]);
    $data[$alias] = $conds;
    sqlGetTable($alias, $fields, $relations, $data);
  } else {
    list($sqlfields, $sqlvalues) = sqlGenerateInsertSet($changes, $fields,
							$relations, $data);

    dbquery("insert into $table ($sqlfields) values ($sqlvalues)");

    if ($autoinc = getAutoIncrement($table)) {
      unset($data[$alias]);
      $data[$alias] = array($autoinc => mysql_insert_id());
      sqlGetTable($alias, $fields, $relations, $data);
    }
  }
}

// returns $table, $conds
function sqlGetConds($alias, $fields, $relations, &$data) {
  if (!isset($relations[$alias])) {
    $table = $alias;
    $conds = array();
  } else if (rel_is_join($alias, $relations)) {
    list($table, $conds) = sqlSplitJoin($relations[$alias],
					$fields, $relations, $data);
  } else if (rel_is_simple($alias, $relations)) {
    $table = $relations[$alias];
    $conds = array();
  } else if (rel_is_update($alias, $relations)) {
    return sqlGetConds($relations[$alias], $fields, $relations, $data);
  } else {
    error("Invalid relation");
  }

  if (isset($data[$alias])) {
    $conds += $data[$alias];
  }

  return array($table, $conds);
}

// returns $table, $conds
function sqlSplitJoin($relation, $fields, $relations, &$data) {
  // find the other alias
  foreach ($relation as $left => $right) {
    if (isset($relations[$right])) {
      $table = $left;
      $other = sqlGetTable($right, $fields, $relations, $data);
      break;
    }
  }

  if (!isset($table)) {
    return array(null, null, null);
  }

  $conds = array();

  // Lookup all the values
  foreach ($relation as $id => $col) {
    if ($id == $table || $id[0] == '~') {
      continue;
    }
    if (isset($other[sqlColumn($col)])) {
      $conds[$id] = $other[$col];
    } else {
      $conds[$id] = $col;
    }
  }

  return array($table, $conds);
}

// Generate update query-part
function sqlGenerateConditionSet($conds, $merge, $fields, $relations, $data) {
  $set = "";

  foreach ($conds as $id => $val) {
    if (!empty($set)) {
      $set .= $merge;
    }
    if (is_array($val)) {
      $val = sqlGetValue($id, $val + $fields, $relations, $data);
    }

    if (is_null($val))
      $set .= dbprep("$id is null");
    else
      $set .= dbprep("$id = %s", $val);
  }

  return $set;
}

// Generate insert query-parts (returns $sqlfields, $sqlvalues)
function sqlGenerateInsertSet($conds, $fields, $relations, &$data) {
  $sqlfields = "";
  $sqlvalues = "";

  foreach ($conds as $id => $val) {
    if (!empty($sqlfields)) {
      $sqlfields .= ", ";
      $sqlvalues .= ", ";
    }
    
    $sqlfields .= sqlColumn($id);
    if (is_array($val)) {
      $keys = array_keys($val);
      if (empty($val) || !is_int($keys[0]))
	$sqlvalues .= sqlGetValue($id, $val + $fields, $relations, $data);
      else
	$sqlvalues .= $val[$keys[0]];
    } else
      $sqlvalues .= dbprep("%s", $val);
  }

  return array($sqlfields, $sqlvalues);
}

function sqlColumn($col) {
  if (mb_strpos($col, '~') !== false)
    return substr($col, 0, mb_strpos($col, '~'));
  return $col;
}

/***** Table Relationships *****/

function rel_is_simple($alias, $relations) {
  return !is_array($relations[$alias]) &&
    !isset($relations[$relations[$alias]]);
}

// change to rel_is_joined
function rel_is_join($alias, $relations) {
  return is_array($relations[$alias]);
}

// change to rel_is_relies
function rel_is_update($alias, $relations) {
  return !is_array($relations[$alias]) &&
    isset($relations[$relations[$alias]]);
}  

function rel_get_joined_type($alias, $relations, $isInsert) {
  if (rel_is_join($alias, $relations) &&
      isset($relations[$alias]['~isInsert']))
    return $relations[$alias]['~isInsert'];
  else
    return $isInsert;
}

?>