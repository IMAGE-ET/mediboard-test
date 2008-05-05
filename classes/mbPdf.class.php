<?php

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Alexis Granger
 *  @version $Revision: $
 */

define ("K_TCPDF_EXTERNAL_CONFIG", "config_externe");

global $dPconfig;

define ("K_PATH_MAIN", $dPconfig['root_dir']."/lib/tcpdf/");
define ("K_PATH_URL", "http://".$dPconfig['site_domain']);

define ("FPDF_FONTPATH", K_PATH_MAIN."fonts/");
define ("K_PATH_CACHE", K_PATH_MAIN."cache/");
define ("K_PATH_URL_CACHE", K_PATH_URL."cache/");
define ("K_PATH_IMAGES", K_PATH_MAIN."../../images/pictures/");
define ("K_BLANK_IMAGE", K_PATH_IMAGES."_blank.png");

define ("HEAD_MAGNIFICATION", 1.1);
define ("K_CELL_HEIGHT_RATIO", 1.25);
define ("K_TITLE_MAGNIFICATION", 1.3);
define ("K_SMALL_RATIO", 2/3);


require_once('./lib/tcpdf/config/lang/eng.php');
require_once('./lib/tcpdf/tcpdf.php');


function asc_shift($str, $offset=0) {
  $new = '';
  for ($i = 0; $i < strlen($str); $i++)
      $new .= chr(ord($str[$i])+$offset);
  return $new;
}


// Classe de gestion des pdf heritant de TCPDF
class CMbPdf extends TCPDF {
	
	public function initMarge($headerMarge, $footerMarge, $autoPageBreak = 25, $imageScale = 4){
	  $this->SetAutoPageBreak(TRUE, $autoPageBreak);
      $this->SetHeaderMargin($headerMarge);
      $this->SetFooterMargin($footerMarge);
      $this->setImageScale($imageScale);
	}
	
	public function writeBarcode($x, $y, $w, $h, $type, $style, $font, $xres, $code) {
	  parent::writeBarcode($x, $y, $w, $h, $type, $style, $font, $xres, asc_shift($code, -2));
	}
}


?>