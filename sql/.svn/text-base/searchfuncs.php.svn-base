<?php

/*****
 * where_*($field, $obj, $fuzz) := [WHERES]
 *   returns a structure for finding entries where field ~= obj
 * [WHERES] = array(<where condition> => <table>)
 *   describes a complicated sql combination of tables
 *****/

// finds a sufficiently close match of an object in table
// to the data, given an array of where functions, checks
function find_match($table, $idcol, $data, $winscore, $checks) {
  $scores = array(0 => 0);

  for ($ii = 0; $ii < 3; $ii++) {
    foreach ($checks as $key => $wherefcn) {
      if (!$data[$key])
	continue;

      // add in the information
      $wheres = $wherefcn($key, $data[$key], $ii);
      if (empty($wheres))
	continue;
      if (is_string($wheres))
	$wheres = array($wheres => '');
      $wheres[] = $table; 
      list($tables, $wheres) = extract_sql($wheres);
      score_matches($scores, 1 / ($ii^2 + 1),
		    sqlgetarray($tables, $idcol, $key, $wheres));
      
      // see if we have a winner
      $bestid = 0;
      $nextid = 0;
      foreach ($scores as $id => $score)
	if ($score > $scores[$bestid]) {
	  $nextid = $bestid;
	  $bestid = $id;
	}

      if ($scores[$bestid] - $scores[$nextid] >= $winscore)
	return $bestid;
    }
  }

  return null;
}

function find_all_matches($table, $idcol, $data, $wherefcn, $col, $fuzz) {
  $table .= ' as basetbl';
  $idcol = 'basetbl.' . $idcol;
  $col = 'basetbl.' . $col;
  // add in the information
  $wheres = $wherefcn($col, $data, $fuzz);
  if (empty($wheres))
    continue;
  if (is_string($wheres))
    $wheres = array($wheres => '');
  $wheres[] = $table;
  list($tables, $wheres) = extract_sql($wheres);
  return sqlgetarray($tables, $idcol, $col, $wheres);
}

// generate the sql that describes the table combination in wheres
function generate_sql($fields, $wheres) {
  list($tables, $wheres) = extract_sql($wheres);
  return "SELECT $fields FROM $tables WHERE $wheres";
}

// extract the string of tables and where condtion from wheres
function extract_sql($wheres) {
  $tables = "";
  $where = "";

  if (is_string($wheres))
    return array('', $wheres);

  foreach ($wheres as $wpart => $set) {
    if (is_array($set)) {
      list($newtable, $newwhere) = extract_sql($set);
      $newwhere .= " AND " . $wpart;
    } else {
      $newtable = $set;
      $newwhere = $wpart;
    }

    if (!empty($newtable)) {
      if (empty($tables))
	$tables = $newtable;
      else
	$tables .= ", " . $newtable;
    }

    if (!is_int($newwhere)) {
      if (empty($where))
	$where = $newwhere;
      else
	$where .= " AND " . $newwhere;
    }
  }

  return array($tables, $where);
}

// add up all the matches
function score_matches(&$scores, $score, $matches) {
  foreach ($matches as $id => $val)
    $scores[$id] = $scores[$id] + $score;
}

function where_duration_derived($field, $durn, $fuzz) {
  switch($fuzz) {
  case 0:
    return array("${field} = ${durn}" => null);
  case 1:
    return array("abs(${field} - ${durn}) < 10*60*60" => null);
  case 2:
    return array("${field} / ${durn} < 1.5 and ${field} / ${durn} > .67" =>
		 null);
  case 3:
    return array("${field} / ${durn} < 2.5 and ${field} / ${durn} > .4" =>
		 null);
  default:
    return array();
  }
}

function where_date_derived($field, $date, $fuzz) {
  switch ($fuzz) {
  case 0:
    return array("unix_timestamp(${field}) = ${date}" => null);
  case 1:
    return array("abs(unix_timestamp(${field}) - ${date}) < 60*60" => null);
  case 2:
    return array("abs(unix_timestamp(${field}) - ${date}) < 24*60*60" => null);
  case 3:
    return array("abs(unix_timestamp(${field}) - ${date}) < 32*24*60*60" =>
		 null);
  default:
    return array();
  }
}

function where_string_derived($field, $str, $fuzz) {
  switch ($fuzz) {
  case 0:
    return array("${field} = '${str}'" => null);
  case 1:
    return array("soundex(${field}) = soundex('${str}')" => null);
  case 2:
    return array("instr('${str}', ${field}) or instr(${field}, '${str}')" =>
		 null);
  case 3:
    $newstr = strtr($str, " '\"\\\n\r\t\$~`!@#^&*()[]{}|:;<>,.?/",
		    "%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%");
    return array("${field} like '%${newstr}%'" => null);
  default:
    return array();
  }
}

?>