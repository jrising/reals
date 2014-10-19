<html>
  <head>
    <link rel="stylesheet" type="text/css" href="../themes/style.css" />
  </head>
  <body>

<?php

$common = "../../";

include_once("thermo.php");

echo "<table border=0 cellpadding=0 cellspacing=0>";

for ($bits = 1; $bits < 5; $bits++) {
  echo "<tr>";
  for ($val = 0; $val < pow(2, $bits); $val++) {
    $values = array();
    for ($bi = 0; $bi < $bits; $bi++)
      $values[] = $val & pow(2, $bi);
    echo "<td>" . drawThermo($values, true) . $val . "</td>";
  }
  echo "</tr>";
}

echo "</table>";

?>

  </body>
</html>