<?php

require_once("templates.php");

function enctag($tag, $attr = array(), $content = '') {
  if (is_array($content)) {
    $output = '';
    foreach ($content as $subcont) {
      $output .= enctag($tag, $attr, $subcont);
    }
    return $output;
  }
    
  if ($content === false)
    return "</$tag>\n";
  
  $str = "";
  foreach ($attr as $key => $val)
    $str .= " $key=\"$val\"";
  
  if ($content === true)
    return "<$tag$str>\n";

  $remfuln = str_replace("\r\n", "\\RN", $content);
  $scontent = "\t" . implode("\r\t", explode("\r", $remfuln));
  $addfuln = str_replace("\\RN", "\r\n", $scontent);

  return "<$tag$str>\r$addfuln\r</$tag>";
}

function inltag($tag, $attr = array(), $content = '') {
  if (is_array($content)) {
    $output = '';
    foreach ($content as $subcont) {
      $output .= inltag($tag, $attr, $subcont) . "\n";
    }
    return $output;
  }

  if ($content === false)
    return "</$tag>";
  
  $str = "";
  foreach ($attr as $key => $val)
    $str .= " $key=\"$val\"";
  
  if ($content === true)
    return "<$tag$str>";

  return "<$tag$str>$content</$tag>";
}

function soltag($tag, $attr = array()) {
  $str = "";
  foreach ($attr as $key => $val)
    $str .= " $key=\"$val\"";
  
  return "<$tag$str />";
}

function html($content, $attr = array()) {
  $tags = GetReplaces('headtags');
  if (!is_null($tags))
    $attr += $tags;
  return enctag('html', $attr, $content);
}

function head($content, $attr = array()) {
  return enctag('head', $attr, $content . GetReplaces('head'));
}

function body($content, $attr = array()) {
  $tags = GetReplaces('bodytags');
  if (!is_null($tags))
    $attr += $tags;
  return enctag('body', $attr, $content);
}

// Create a given classed span tag
function span($content, $class = null, $attr = array()) {
  if (!is_null($class))
    $attr['class'] = $class;
  return inltag('span', $attr, $content);
}

// Create a given classed div tag
function div($content, $class = null, $attr = array()) {
  if (!is_null($class))
    $attr['class'] = $class;
  return enctag('div', $attr, $content);
}

function font($content, $attr = array()) {
  return inltag('font', $attr, $content);
}

function table($content, $attr = array()) {
  if (!isset($attr['style'])) {
    if (!isset($attr['cellpadding']))
      $attr['cellpadding'] = 0;
    if (!isset($attr['cellspacing']))
      $attr['cellspacing'] = 0;
    if (!isset($attr['border']))
      $attr['border'] = 0;
  }
  return enctag('table', $attr, $content);
}

function tr($content, $attr = array()) {
  return enctag('tr', $attr, $content);
}

function th($content, $attr = array()) {
  return enctag('th', $attr, $content);
}

function td($content, $attr = array()) {
  return enctag('td', $attr, $content);
}

function ul($content, $attr = array()) {
  return enctag('ul', $attr, $content);
}

function li() {
  return soltag('li');
}

function img($src, $attr = array()) {
  return soltag('img', array('src' => $src) + $attr);
}

function hr() {
  return soltag('hr');
}

function br() {
  return soltag('br');
}

function p($content, $attr = array()) {
  return inltag('p', $attr, $content);
}

function h1($content) {
  return inltag('h1', array(), $content);
}

function i($content) {
  return inltag('i', array(), $content);
}

function b($content) {
  return inltag('b', array(), $content);
}

function u($content) {
  return inltag('u', array(), $content);
}

// basic link
function l($url, $name = null) {
  if (is_null($name))
    return inltag('a', array('href' => $url), $url);
  else
    return inltag('a', array('href' => $url), $name);
}

function center($content, $attr = array()) {
  return inltag('center', $attr, $content);
}

function ln($content) {
  return $content . "\n";
}

function title($content) {
  return inltag('title', array(), $content);
}

function csslink($url) {
  return soltag('link', array('rel' => "stylesheet", 'href' => $url));
}

function debugout($text) {
  echo $text;
}

function spacer($x, $y) {
  return table(tr(td("", array('width' => $x, 'height' => $y))));
}

function hspace($x) {
  return div("", null, array('width' => $x));
}

?>