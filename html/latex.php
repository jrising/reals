<?php

function LaTeX2HTML($text) {
  $pregtrans = array('/\\\\euros\{([^\}]*)\}/' => '&euro;\1',
		     '/\$([^\^\$]*)\^([^\$])\$/' => '\1<sup>\2</sup>');

  $text = preg_replace(array_keys($pregtrans), array_values($pregtrans), $text);

  $translate = array('\`{A}' => '&Agrave;', '\`{a}' => '&agrave;',
		     '\\\'{A}' => '&Aacute;', '\\\'{a}' => '&aacute;',
		     '\^{A}' => '&Acirc;', '\^{a}' => '&acirc;',
		     '\^{I}' => '&Icirc;', '\^{i}' => '&icirc;',
		     '\\\'{E}' => '&Eacute;', '\\\'{e}' => '&eacute;',
		     '\`{E}' => '&Egrave;', '\`{e}' => '&egrave;',
		     '\^{O}' => '&Ocirc;', '\^{o}' => '&ocirc;',
		     '\"{O}' => '&Ouml;', '\"{o}' => '&ouml;',
		     '\"{U}' => '&Uuml;', '\"{u}' => '&uuml;',
		     '\euros' => '&euro;', '\pounds' => '&pound;');

  return str_replace(array_keys($translate), array_values($translate), $text);
}

?>