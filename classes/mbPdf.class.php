<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
function is_utf8 ($str) {
  return (utf8_encode(utf8_decode($str)) == $str);
}

function to_utf8($str) {
  return is_utf8($str)?$str:utf8_encode($str);
}

// Classe de gestion des pdf heritant de TCPDF
class CMbPdf extends TCPDF {
  public function Text($x, $y, $txt, $stroke=0, $clip=false) {
    parent::Text($x, $y, to_utf8($txt), $stroke, $clip);
  }
  
  public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0) {
    return parent::Cell($w, $h, to_utf8($txt), $border, $ln, $align, $fill, $link, $stretch);
  }
    
  public function Write($h, $txt, $link='', $fill=0, $align='', $ln=false, $stretch=0) {
    return parent::Write($h, to_utf8($txt), $link, $fill, $align, $ln, $stretch);
  }
  
  public function WriteBarcodeGrid($x, $y, $width, $height, $col_num, $row_num, $data) {
  	$cell_width = $width / $col_num;
  	$cell_height = $height / $row_num;
  	$i = 0;

  	$delta_x = 0;
  	$delta_y = 0;
    $this->SetFontSize(7);
    $this->SetDrawColor(20, 20, 20);
    
    $barcode_height = 8;
    $barcode_width_ratio = 0.8;
    
  	foreach ($data as $cell) {
  		if ($i % $col_num == 0 && $i != 0) {
  			$y += $cell_height;
  			$delta_x = 0;
  		}
  		
  		$line_height = ($cell_height - $barcode_height) / (count($cell));
  		
  	  $this->Rect($x + $delta_x, $y + $delta_y, $cell_width, $cell_height);
  		foreach ($cell as $line) {
  			// if it's a barcode
  			if (is_array($line)) {
  				// draw barcode
  				$this->writeBarcode($x + $delta_x + ($cell_width*(1 - $barcode_width_ratio)/ 2), 
  				                    $y + $delta_y, $cell_width*$barcode_width_ratio, 
  				                    $barcode_height, $line['type'], false, false, 2, $line['barcode']);
  				                    
  				$delta_y += $barcode_height;
  				
  				$this->writeHTMLCell(0, $line_height, $x + $delta_x + ($cell_width*(1 - $barcode_width_ratio) / 2), 
  				                           $y + $delta_y, str_replace("x",' ',$line['barcode']), 0, 0, 0);
  				                           
  				$delta_y += $line_height;
  			}
  			else {
	  			// print line
	  			$this->writeHTMLCell(0, $line_height, $x + $delta_x + ($cell_width*(1 - $barcode_width_ratio) / 2), 
	  			                           $y + $delta_y, utf8_encode($line), 0, 0, 0);
	  			                           
	  			$delta_y += $line_height;
  			}
  		}
  		$delta_y = 0;
  		$delta_x += $cell_width;
  		$i++;
  	}
  }
}


?>