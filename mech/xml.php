<?php

/* Translates...

<foo bar="bazz"><quxx>snap</quxx><crackle pop="pop" /></foo>

into...

array("" => "foo", "bar" => "bazz",
      0 => array("" => "quxx", 0 => "snap"),
      1 => array("" => "crackle", "pop" => "pop"))

*/

// <foo>snap</foo> --> array("" => "foo", 0 => "snap")
// <foo bar="bazz">snap</foo> --> array("" => "foo", "bar" => "bazz", 0 => "snap")
// <foo><bar>snap</bar></foo> --> array("" => "foo", 0 => array("" => "bar", 0 => "snap"))
// <foo><bar>snap</bar><bar>crackle</bar></foo> -->
//   array("" => "foo", 0 => array("" => "bar", 0 => "snap"), 1 => array("" => "bar", 0 => "crackle"))
// <foo><bar>snap</bar>crackle</foo> --> array("" => "foo", 0 => array("" => "bar", 0 => "snap"), 1 => "crackle")				

function ParseFullXML($xml) {
  if (preg_match("/^\<\?xml\s[^\?]*\?\>/", $xml, $matches)) {
    $offset = mb_strlen($matches[0]);
  } else {
    $offset = 0;
  }

  return ParseXML($xml, $offset);
}

// Return the first branche of xml that matches the given conditions
// $conds is {name => {key => value}} of nested flags
function SearchXML($conds, $parsed) {
  $search = $parsed;

  foreach ($conds as $name => $attr) {
    $foundmatch = false;

    // look for this condition
    foreach ($search as $item => $branch) {
      if (is_int($item) && $branch[""] == $name) {
	// check if other conditions match
	if (is_array($attr)) {
	  $foundmatch = true;
	  foreach ($attr as $key => $value)
	    if (!isset($branch[$key]) || $branch[$key] == $value) {
	      $foundmatch = false;
	      break;
	    }
	
	  if (!$foundmatch)
	    continue;
	} else
	  $foundmatch = true;
	
	// found a match!
	$search = $branch;
	break;
      }
    }

    if (!$foundmatch)
      return null;
  }

  return $search;
}

function ParseXML($xml, &$offset, $endtag = "") {
  $pregend = preg_quote($endtag, '/');

  if (preg_ssmatch("/^\<\s*([^\/\>\s]+)/", $xml, $matches, 0, $offset)) {
    // Beginning of a new tag
    $tag = $matches[1];
    $offset += mb_strlen($matches[0]);

    // Grab the attributes
    $result = array();
    while (preg_ssmatch("/^\s*([^\=\/\>\s]+)\s*\=\s*'([^']*)'/", $xml,
		      $matches, 0, $offset) ||
	   preg_ssmatch('/^\s*([^\=\/\>\s]+)\s*\=\s*"([^"]*)"/', $xml,
		      $matches, 0, $offset)) {
      $result[$matches[1]] = $matches[2];
      $offset += mb_strlen($matches[0]);
    }

    // Add the name
    $result[""] = $tag;

    // Is this a contentless tag?
    if (preg_ssmatch("/^\s*\/\s*\>/", $xml, $matches, 0, $offset)) {
      $offset += mb_strlen($matches[0]);
      return $result;
    } else if (preg_ssmatch("/^\s*\>/", $xml, $matches, 0, $offset)) {
      $offset += mb_strlen($matches[0]);
    } else {
      echo "XML Attribute Syntax error at $offset";
      return null;
    }

    // Now add all the lines
    while (!is_null($line = ParseXML($xml, $offset, $tag))) {
      $result[] = $line;
    }
    return $result;
  } else if (preg_ssmatch("/^<\/\s*$pregend\s*\>/", $xml,
			$matches, 0, $offset)) {
    // End of the tag!
    $offset += mb_strlen($matches[0]);
    return null;
  } else {
    // Just a line
    $eol = strcspn($xml, "\n<", $offset);
    $line = substr($xml, $offset, $eol);
    $offset += $eol;
    if ($xml[$offset] == "\n") {
      $offset++;
    }
    return $line;
  }
}

function preg_ssmatch($pattern, $subject, &$matches, $flags, $offset) {
  return preg_match($pattern, substr($subject, $offset), $matches, $flags);
}

?>