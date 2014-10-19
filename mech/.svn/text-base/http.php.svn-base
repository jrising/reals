<?php

function curl_string_get($url, $args = array()) {
  if (empty($args))
    return curl_string($url);

  $getstr = '?';
  foreach ($args as $key => $value)
    $getstr .= $key . '=' . urlencode($value) . '&';

  // remove the last &
  $getstr = substr($getstr, 0, -1);

  return curl_string($url . $getstr);
}

function curl_string($url) {
  $ch = curl_init(); 
  curl_setopt($ch, CURLOPT_URL, $url); 
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
  //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 120);
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}

?>