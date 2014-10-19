<?php

include_once($commondir . "/sql/sqlfuncs.php");

function logincheck($usercol, $user, $passcol, $pass) { 
  // confirm password
  return sqlgetid('user', 'user_id',
		  "${usercol} = '${user}' and ${passcol} = '${pass}'");
}

?>
