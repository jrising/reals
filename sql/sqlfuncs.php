<?php

function sqlquery($query) {
  /* Connect to Database */
  dbconnect();

  /* Perform Query */
  $result = mysql_query($query) or die("Query failed : " . mysql_error());
}

function sqlinsert($table, $primekey, $columns, $values) {
  /* Connect to Database */
  dbconnect();

  /* Do insertion */
  $query = "insert into " . $table . " (" . str_replace("/,", ",", $columns) . ") values (" . str_replace("/,", ",", $values) . ");";
  $result = mysql_query($query) or die("Insert failed : " . mysql_error());

  /* Get and return id */
  return mysql_insert_id();
}

function sqlupdate($table, $primekey, $id, $columns, $values) {
  /* Connect to Database */
  dbconnect();

  $updates = "set ";
  $listcols = explode("/,", $columns);
  $listvals = explode("/,", $values);
  for ($i = 0; $i < count($listcols); $i++) {
    if ($i != 0)
      $updates = $updates . ", ";
    $updates = $updates . $listcols[$i] . " = " . $listvals[$i];
  }

  /* Do update */
  $query = "update " . $table . " " . $updates . " where $primekey = $id;";
  $result = mysql_query($query) or die("Insert failed : " . mysql_error());
}

function sqlgetarray($table, $idcol, $column, $where = false) {
  dbconnect();
  if ($where && mb_strpos($where, "where"))
    $query = "select ${idcol}, ${column} from ${table}, ${where}";
  else if ($where)
    $query = "select ${idcol}, ${column} from ${table} where ${where}";
  else
    $query = "select ${idcol}, ${column} from ${table}";
  $result = mysql_query($query) or die("Array query failed : " . mysql_error());
  $values = array();
  while ($value = mysql_fetch_array($result, MYSQL_NUM))
    $values[$value[0]] = $value[1];
  return $values;
}

function sqlgetid($table, $idcol, $where) {
  dbconnect();
  $query = "select ${idcol} from ${table} where ${where};";
  $result = mysql_query($query) or die("ID query failed : " . mysql_error());
  $value = mysql_fetch_array($result, MYSQL_NUM);
  if (!$value)
    return $value;
  return $value[0];
}

function sqlget($table, $column, $idcol, $id) {
  dbconnect();
  $query = "select ${column} from ${table} where ${idcol} = ${id}";
  $result = mysql_query($query) or die("Single query failed : " . mysql_error());
  $value = mysql_fetch_array($result, MYSQL_NUM);
  if (!$value)
    return $value;
  return $value[0];
}

function sqlgetall($table, $idcol, $id) {
  dbconnect();
  $query = "select * from ${table} where ${idcol} = ${id}";
  $result = mysql_query($query) or die("Single query failed : " . mysql_error());
  $values = mysql_fetch_array($result, MYSQL_ASSOC);
  return $values;
}

function sqlremove($table, $idcol, $id) {
  dbconnect();
  $query = "delete from ${table} where ${idcol} = ${id}";
  $result = mysql_query($query) or die("Removal failed : " . mysql_error());
  return $result;
}

?>