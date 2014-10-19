<?php

/*
 * The top-level database interface
 */

require_once(comdir('sql/dbfuncs.php'));

function DazeInitialize($relations) {
  global $dazeRelations, $dazeData;

  $dazeRelations = $relations;
  $dazeData = array();
}

function DazeGetRows($alias, $conds,
		     $order = null, $group = null, $limit = null) {
  global $dazeRelations;

  $data = array($alias => $conds);
  list($table, $fullconds) = sqlGetConds($alias, array(),
					 $dazeRelations, $data);

  $where = sqlGenerateConditionSet($fullconds, " and ", array(),
				   $dazeRelations, $data);

  $query = "select * from $table where $where";
  if (!is_null($order))
    $query .= " order by $order";
  if (!is_null($group))
    $query .= " group by $group";
  if (!is_null($limit))
    $query .= " limit $limit";

  $rows = array();

  $result = dbquery($query);
  while ($result && $row = dbfetch($result, DB_ASSOC))
    $rows[] = $row;

  return $rows;
}

function DazeGetValue($alias, $column, $conds) {
  global $dazeRelations;

  $data = array($alias => $conds);
  list($table, $fullconds) = sqlGetConds($alias, array(),
					 $dazeRelations, $data);

  $where = sqlGenerateConditionSet($fullconds, " and ", array(),
				   $dazeRelations, $data);
  
  $query = "select $column from $table where $where";
  $result = dbquery($query);
  $value = null;
  if ($result && $row = dbfetch($result, DB_NUM))
    list($value) = $row;

  return $value;
}

?>