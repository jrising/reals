<html>
  <head>
    <link rel="stylesheet" type="text/css" href="../html/themes/style.css" />
  </head>
  <body>

Beginning tests...<p>

<?php

function dbconnect() {
  /* Connecting, selecting database */
  $link = mysql_connect("localhost", "cadmin", "cadmin")
    or die("Could not connect : " . mysql_error());
  mysql_select_db("cevents") or die("Could not select database");    
}

$common = '../';

require_once($common . 'sql/testing.php');

echo TestSearch(array('Cambridge', 'Caimbredge', 'Camb'), 'locations',
		'location_id', 'title', 'where_string_derived');
echo hr();
echo TestSearch(array(24*60*60, 300*24*60*60, 100*300*24*60*60), 'activities',
		'activity_id', 'duration', 'where_duration_derived');
echo hr();
echo TestSearch(array(time() - 10*24*60*60, time(),
		      time() + 10*30*24*60*60), 'activities',
		'activity_id', 'starttime', 'where_date_derived');

?>

  </body>
</html>