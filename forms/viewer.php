<?php

require_once("control.php");

function makeViewer($ctrl) {
  if (isset($ctrl['view']))
    $ctrl['form'] = $ctrl['view'];
  else
    $ctrl['form'] = 'viewDefault';
  return $ctrl;
}

?>
