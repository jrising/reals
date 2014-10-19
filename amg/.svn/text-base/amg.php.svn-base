<?php

$amgDefaults = array('filefunc' => 'imagegif', 'fileext' => 'gif', 'hash' => 5,
		     'buttoncolor' => array(255, 128, 0),
		     'fontcolor' => array(65, 36, 12),
		     'font' => 'ccaps', 'fsize' => 14);

function amgSetup($dir, $attr = array()) {
  global $amgDefaults, $amgDirectory;

  $amgDefaults += $attr;
  $amgDirectory = $dir;

  putenv('GDFONTPATH=' . realpath(comdir("amg/ttfs")));
}

function amgFile() {
  global $amgDefaults;

  $args = func_get_args();
  $func = array_shift($args);
  $name = array_shift($args);

  $filefunc = $amgDefaults['filefunc'];

  $imgfile = amgFilename($func, $name, $args);
  if (!file_exists($imgfile)) {
    // generate image
    $im = call_user_func_array($func, $args);
    // save to file
    $filefunc($im, $imgfile);
  }

  return $imgfile;
}

function amgNewFile() {
  global $amgDefaults;

  $args = func_get_args();
  $func = array_shift($args);
  $name = array_shift($args);

  $filefunc = $amgDefaults['filefunc'];

  $imgfile = amgFilename($func, $name, $args);
  if (file_exists($imgfile))
    unlink($imgfile);

  // generate image
  $im = call_user_func_array($func, $args);
  // save to file
  $filefunc($im, $imgfile);

  return $imgfile;
}

function amgFilename($func, $name, $args = array()) {
  global $amgDefaults, $amgDirectory;

  $hashtext = $func . ':' . serialize($args);
  $hash = substr(md5($hashtext), 0, 5);

  $fileext = $amgDefaults['fileext'];

  return $amgDirectory . $name . "_$hash.$fileext";
}

?>