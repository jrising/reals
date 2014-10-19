<?php
function fixquotes($htmlarray) {
  foreach ($htmlarray as $key => $val) {
    if (is_array($val))
      $htmlarray[$key] = fixquotes($val);
    else
      $htmlarray[$key] = htmlspecialchars($val, ENT_QUOTES);
  }
  return $htmlarray;
}

function captitle($str) {
  return trim(ucfirst(str_replace(array("Of ","A ","The ","And ","An ", "Or ", "Nor ","But ","If ","Then ","Else ","When ","Up ","At ","From ","By ","On ","Off ","For ","In ","Out ","Over ","To "), array("of ","a ","the ","and ","an ","or ","nor ","but ","if ","then ","else ","when ","up ","at ","from ","by ","on ","off ","for ","in ","out ","over ","to "), ucwords(strtolower($str)))));
}

function makeget($array) {
  if (count($array) == 0)
    return "";

  $getstr = "?";
  foreach ($array as $var => $val) {
    if ($getstr != "?")
      $getstr = $getstr . "&" . $var . "=" . $val;
    else
      $getstr = $getstr . $var . "=" . $val;
  }

  return $getstr;
}

?>