<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:$
* @author
*/
global $AppUI, $can;

CAppUI::requireLibraryFile("phpThumb/phpthumb.class");

$compte_rendu_id = CValue::post("compte_rendu_id");
$compte_rendu = new CCompteRendu();
$compte_rendu->load($compte_rendu_id);

$generate_thumbs = CValue::post("generate_thumbs");
$mode        = CValue::post("mode","doc");
$type        = CValue::post("type");
$footer_id   = CValue::post("footer_id", $compte_rendu->footer_id);
$header_id   = CValue::post("header_id", $compte_rendu->header_id);
$stream      = CValue::post("stream");
$content     = stripslashes(CValue::post("content"));
$page_format = CValue::post("page_format",$compte_rendu->_page_format);
$orientation = CValue::post("orientation",$compte_rendu->_orientation);
$first_time  = CValue::post("first_time");
$user_id     = CValue::post("user_id");
$page_width  = CValue::post("page_width", $compte_rendu->page_width);
$page_height = CValue::post("page_height", $compte_rendu->page_height);
$margins     = CValue::post("margins", array($compte_rendu->margin_top,
                                             $compte_rendu->margin_right,
                                             $compte_rendu->margin_bottom,
                                             $compte_rendu->margin_left));
$file = new CFile;
$files = $file->loadFilesForObject($compte_rendu);

if (count($files)) {
  $file = reset($files);
}

if (!count($files) && $mode != "modele" && $first_time == 1 && !$compte_rendu->object_id) {
  CAppUI::stepAjax(CAppUI::tr("CCompteRendu-no-pdf-generated"));
  return;
	
}
else if (count($files) && $first_time == 1 && file_get_contents($file->_file_path) != '') {
  // rien
}
else {
  if($mode == "modele") {
    $height = CValue::post("height",$compte_rendu->height);
    switch($type) {
      case "header":
      case "footer":
        $content = $compte_rendu->loadHTMLcontent($content, $mode,$type, '',$height,'','',$margins);
        break;
      case "body":
        $compte_rendu_h_f = new CCompteRendu;
        $header = ''; $sizeheader = 0;
        $footer = ''; $sizefooter = 0;
        if($header_id) {
          $compte_rendu_h_f->load($header_id);
          $header = $compte_rendu_h_f->source;
          $sizeheader = $compte_rendu_h_f->height;
        }
        if($footer_id) {
          $compte_rendu_h_f->load($footer_id);
          $footer = $compte_rendu_h_f->source;
          $sizefooter = $compte_rendu_h_f->height;
        }
        $content = $compte_rendu->loadHTMLcontent($content, $mode, $type, $header,$sizeheader,$footer,$sizefooter,$margins);
      }
    }
  else {
    $content = $compte_rendu->loadHTMLcontent($content, $mode,'','','','','',$margins);
  }

  // Traitement du format de la page
  if($orientation == "landscape") {
    $a = $page_width;
    $page_width = $page_height;
    $page_height = $a;
  }
  if($page_format == "") {
    $page_width  = round((72 / 2.54) * $page_width, 2);
    $page_height = round((72 / 2.54) * $page_height, 2);
    $page_format = array(0, 0, $page_width, $page_height);
  }

  // Création du CFile si inexistant
  if (!count($files)) {
    $file = new CFile();
    $file->setObject($compte_rendu);
    $file->file_name  = $compte_rendu->nom;
    $file->file_type  = "application/pdf";
    $file->file_owner = $user_id;
    $file->fillFields();
    $file->updateFormFields();
    $file->forceDir();
  }
  else {
    $file = reset($files);
  }

  $htmltopdf = new CHtmlToPDF;
  $htmltopdf->generate_pdf($content, $stream, $page_format, $orientation, $file->_file_path);
  $file->file_size = filesize($file->_file_path);
  $file->store();
}

// Traitement des vignettes
$file->loadNbPages();

if($generate_thumbs){
  $thumbs = new phpthumb();
  $thumbs->setSourceFilename($file->_file_path);

  $vignettes = array();
  for($i = 0; $i < $file->_nb_pages ; $i++) {

    $thumbs->sfn=$i ;
    $thumbs->w = 138;
    $thumbs->GenerateThumbnail();
    $vignettes[$i] = base64_encode($thumbs->IMresizedData);
  }

  $smarty = new CSmartyDP();
  $smarty->assign("vignettes",$vignettes);
  $smarty->assign("nbpages", $file->_nb_pages);
  $smarty->assign("file_id", $file->_id);
  $smarty->assign("compte_rendu_id", $compte_rendu_id);
  $smarty->assign("can", $can);
  $smarty->display("inc_thumbnail.tpl");
}

?>