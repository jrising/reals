<?php

require_once($common . 'html/simple.php');
require_once($common . 'sql/searchfuncs.php');
require_once($common . 'sql/sqlfuncs.php');
require_once($common . 'html/thermo/thermo.php');


function TestSearch($samples, $table, $idcol, $matchcol, $matchfunc) {
  /* These tests use the original database */
  $output = table(true);
  $row = td('&nbsp;');
  foreach ($samples as $str)
    $row .= th($str);
  $output .= tr($row);

  $allitems = sqlgetarray($table, $idcol, $matchcol);
  foreach ($allitems as $locid => $title) {
    $useit = false;
    $varray = array();
    foreach ($samples as $ii => $str) {
      $values = array();
      for ($fuzz = 0; $fuzz < 4; $fuzz++) {
	echo '(';
	$matches = find_all_matches($table, $idcol, $str,
				    $matchfunc, $matchcol, $fuzz);

	echo "$fuzz";
	if ($ii == count($samples) - 1) {
	  $wheres = $matchfunc($matchcol, 'x', $fuzz);
	  list($tables, $wheres) = extract_sql($wheres);
	  $key = $wheres;
	} else
	  $key = $fuzz;

	if (isset($matches[$locid])) {
	  $useit = true;
	  $values[$key] = true;
	} else {
	  $values[$key] = false;
	}
	echo ")";
      }
      $varray[] = $values;
    }

    if ($useit) {
      $row = th($title);
      foreach ($varray as $values)
	$row .= td(drawThermo($values, true));
      $output .= tr($row);
    }
  }

  $output .= table(false);

  return $output;
}

?>