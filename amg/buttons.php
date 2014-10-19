<?php

require_once("amg.php");

function amgButton($text, $width, $height, $attr = array()) {
  global $amgDefaults;
  $options = $amgDefaults + $attr;

  $fsize = $options['fsize'];
  $color = $options['buttoncolor'];

  $im = imagecreatetruecolor($width, $height);

  $bordcolor = imagecolorallocate($im, 0, 0, 0);
  $backcolor = imagecolorallocate($im, $color[0], $color[1], $color[2]);

  imagefilledrectangle($im, 0, 0, $width, $height, $backcolor);
  imagerectangle($im, 0, 0, $width, $height, $bordcolor);

  amgAddButtonText($im, $text, $fsize, $attr);

  return $im;
}

function amgButtonModify($source, $text, $attr = array()) {
  global $amgDefaults;
  $options = $amgDefaults + $attr;

  $size = $options['fsize'];

  $im = imagecreatefrompng($source);

  $trans = imagecolorallocate($im, 0, 0, 0);
  imagecolortransparent($im, $trans);

  amgAddButtonText($im, $text, $size, $attr);

  return $im;
}

function amgAddButtonText($im, $text, $size, $attr = array()) {
  global $amgDefaults;
  $options = $amgDefaults + $attr;

  $font = $options['font'];
  $color = $options['fontcolor'];

  $textcolor = imagecolorallocate($im, $color[0], $color[1], $color[2]);
  $tshdcolor = imagecolorallocate($im, (255 + $color[0]) / 2,
				  (255 + $color[1]) / 2, 
				  (255 + $color[2]) / 2);

  //amgAddCenterText($im, $text, -1, -1, $size, $tshdcolor, $font, $attr);
  //amgAddCenterText($im, $text, 0, -1, $size, $tshdcolor, $font, $attr);
  //amgAddCenterText($im, $text, -1, 0, $size, $tshdcolor, $font, $attr);
  amgAddCenterText($im, $text, 0, 0, $size, $textcolor, $font, $attr);

  return true;
}

function amgAddCenterText($im, $text, $dx, $dy, $size, $color, $font,
			  $attr = array()) {
  list($xll, $yll, $xlr, $ylr, $xur, $yur, $xul, $yul) =
    imagettfbbox($size, 0, $font, $text);
  
  $x0 = (imagesx($im) - ($xlr - $xll)) / 2 + $dx;
  $y0 = (imagesy($im) - ($yul - $yll)) / 2 + $dy;

  imagettftext($im, $size, 0, $x0, $y0, $color, $font, $text);

  return true;
}

?>