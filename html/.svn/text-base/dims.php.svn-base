<?php

// max is in standard character widths
function limit($text, $max) {
  // based off default of 7 pixels per character
  $lcases = array('f' => -4/7, 'i' => -4/7, 'j' => -4/7, 'l' => -4/7,
		  'm' => 4/7, 'r' => -3/7, 't' => -3/7, 'v' => -2/7,
		  'w' => 2/7,
		  'A' => 2/7, 'B' => 2/7, 'C' => 2/7, 'D' => 2/7,
		  'E' => 2/7, 'F' => 1/7, 'G' => 3/7, 'H' => 2/7,
		  'I' => -4/7, 'J' => -1/7, 'K' => 2/7, 'M' => 4/7,
		  'N' => 2/7, 'O' => 3/7, 'P' => 2/7, 'Q' => 3/7,  
		  'R' => 2/7, 'S' => 2/7, 'U' => 2/7, 'V' => 2/7,
		  'W' => 6/7, 'Y' => 2/7,
		  ' ' => -3/7, '.' => -3/7);
  $histogram = count_chars($text);
  $length = mb_strlen($text);
  foreach ($histogram as $byte => $count) {
    if (isset($lcases[chr($byte)])) {
      $length += $count * $lcases[chr($byte)];
    }
  }
  
  // try to get as close as possible
  if ($length > $max) {
    $perchar = $length / mb_strlen($text); // length / char
    // length / (length / char): .5 is for ... taking up 4/7 length
    $numlose = floor(($max - .5 - $length) / $perchar);
    if ($numlose <= 3) {
      $numlose = 4; // never go into infinite loop
    }
    $newtext = substr($text, 0, -$numlose) . '...';
    // how does this compare to num characters?
    return limit($newtext, $max);
  } else {
    return $text;
  }
}

function cm($x) {
  return in($x) * 2.54;
}

function in($x) {
  return 72 * $x;
}

?>