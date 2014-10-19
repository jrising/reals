<?php

function html_r($arr) {
  return nl2br(eregi_replace(" ", " ", print_r($arr, TRUE)));   
}

function aget($arr, $key) {
  if (isset($arr[$key]))
    return $arr[$key];
  else
    return null;
}

?>