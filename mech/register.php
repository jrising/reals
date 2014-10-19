<?php

function check_free_username($usercol, $chosen) {
  // check if username is already taken
  dbconnect();
  $query = "select * from user where ${usercol} = '${chosen}'";
  $result = mysql_query($query) or die("Query failed : " . mysql_error());
  $numhere = mysql_fetch_array($result, MYSQL_NUM);
  return ($numhere[0] == 0);
}

?>