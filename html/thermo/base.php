<?php

require_once($common . 'html/simple.php');

function drawThermo($values, $showKeys = false) {
  global $common;
  $toimg = $common . 'html/thermo/';

  $keys = array_keys($values);
  $last = count($keys) - 1;

  if ($showKeys)
    $label = td(span($keys[$last], 'small'));
  else
    $label = "";

  $tower = "";
  if ($values[$keys[$last]])
    $tower = tr(td(img($toimg . "thermotop1.gif")) . $label);
  else if (isset($keys[$last - 1]) && isset($values[$keys[$last - 1]]) &&
	   !$values[$keys[$last - 1]])
    $tower = tr(td(img($toimg . "thermotop00.gif")) . $label);
  else
    $tower = tr(td(img($toimg . "thermotop10.gif")) . $label);

  for ($i = $last - 1; $i >= 0; $i--) {
    if ($showKeys)
      $label = td(span($keys[$i], 'small'));
    else
      $label = "";

    if ($values[$keys[$i]])
      $tower .= tr(td(img($toimg . "thermo1.gif")) . $label);
    else if (($i == 0 || $values[$keys[$i - 1]]) && $values[$keys[$i + 1]])
      $tower .= tr(td(img($toimg . "thermo101.gif")) . $label);
    else if ($i == 0 || $values[$keys[$i - 1]])
      $tower .= tr(td(img($toimg . "thermo100.gif")) . $label);
    else if ($values[$keys[$i + 1]])
      $tower .= tr(td(img($toimg . "thermo001.gif")) . $label);
    else
      $tower .= tr(td(img($toimg . "thermo000.gif")) . $label);
  }

  if ($showKeys)
    $tower .= tr(td(img($toimg . "thermobot.gif")) . td('&nbsp;'));
  else
    $tower .= tr(td(img($toimg . "thermobot.gif")));    

  return "<table border=0 cellspacing=0 cellpadding=0>" . $tower . "</table>";
}

?>