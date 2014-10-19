<?php

// return attribute string with css data
function css($opt) {
  if (is_string($opt)) {
    if (mb_strpos($opt, ':') === false)
      return array('class' => $opt);
    else
      return array('style' => $opt);
  } else if (is_array($opt)) {
    $style = "";
    foreach ($opt as $key => $val) {
      if ($style != "")
	$style .= '; ';
      $style = "$key: $val";
    }

    return array('stile' => $style);
  }
}

function CassDefault() {
  $args = func_get_args();
  while (count($args) > 1) {
    $class .= ' .' . array_shift($args);
  }

  
}

?>