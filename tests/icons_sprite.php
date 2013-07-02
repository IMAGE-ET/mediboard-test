<?php

die;

$size_orig = 48;
$ttl = 60*60; // seconds

$size = isset($_GET["size"]) ? $_GET["size"] : $size_orig;
$size = min($size, $size_orig);

$icons = glob("../modules/*/images/icon.png");

$hash = dechex(crc32(implode("", $icons)));
$cachefile = "../tmp/$hash-sprite-$size.png";

$out_of_date = !file_exists($cachefile) || (filemtime($cachefile)+$ttl < time());

if (!$out_of_date) {
  $last_update = filemtime($cachefile);
  
  foreach($icons as $_icon) {
    if ($last_update < filemtime($_icon)) {
      $out_of_date = true;
      break;
    }
  }
}

if ($out_of_date) {
  $img = imagecreatetruecolor($size*count($icons), $size);
  $background = imagecolorallocate($img, 0, 0, 0);
  imagecolortransparent($img, $background); // make the new temp image all transparent
  imagesavealpha($img, true);
  imagealphablending($img, false);
  
  foreach($icons as $i => $_icon) {
    $_img = imagecreatefrompng($_icon);
    
    if ($size != $size_orig) {
      imagecopyresampled($img, $_img, $i*$size, 0, 0, 0, $size, $size, $size_orig, $size_orig);
    }
    else {
      imagecopy($img, $_img, $i*$size, 0, 0, 0, $size_orig, $size_orig);
    }
  }
  
  ob_start();
  imagepng($img);
  $content = ob_get_clean();
  
  file_put_contents($cachefile, $content);
}
