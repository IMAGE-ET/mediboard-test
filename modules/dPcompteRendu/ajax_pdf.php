<?php

/**
 * Génération d'un PDF à partir d'une source html
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */
$compte_rendu_id = CValue::post("compte_rendu_id");

// On s'arrête là si pas d'id
if (!$compte_rendu_id) {
  CApp::rip();
}

$compte_rendu = new CCompteRendu();
$compte_rendu->load($compte_rendu_id);

if (!$compte_rendu->_id) {
  CAppUI::stepAjax(CAppUI::tr("CCompteRendu-alert_doc_deleted"));
  CApp::rip();
}

$compte_rendu->loadContent();
$generate_thumbs = CValue::post("generate_thumbs");
$mode        = CValue::post("mode", "doc");
$print       = CValue::post("print", 0);
$type        = CValue::post("type", $compte_rendu->type);
$preface_id  = CValue::post("preface_id", $compte_rendu->preface_id);
$ending_id   = CValue::post("ending_id" , $compte_rendu->ending_id);
$header_id   = CValue::post("header_id" , $compte_rendu->header_id);
$footer_id   = CValue::post("footer_id" , $compte_rendu->footer_id);
$stream      = CValue::post("stream");
$content     = stripslashes(urldecode(CValue::post("content", null)));
if (!$content) {
  $content = $compte_rendu->_source;
}

$save_content = $content;

$ids_corres  = CValue::post("_ids_corres");
$write_page  = CValue::post("write_page", 0);
$update_date_print = CValue::post("update_date_print", 0);
$page_format = CValue::post("page_format", $compte_rendu->_page_format);
$orientation = CValue::post("orientation", $compte_rendu->_orientation);
$first_time  = CValue::post("first_time");
$user_id     = CValue::post("user_id", CAppUI::$user->_id);
$page_width  = CValue::post("page_width", $compte_rendu->page_width);
$page_height = CValue::post("page_height", $compte_rendu->page_height);
$margins     = CValue::post(
  "margins", array (
    $compte_rendu->margin_top,
    $compte_rendu->margin_right,
    $compte_rendu->margin_bottom,
    $compte_rendu->margin_left
    )
);

if ($ids_corres) {
  $ids = explode("-", $ids_corres);
  $_GET["nbDoc"] = array();
  foreach ($ids as $doc_id) {
    if ($doc_id) {
      $_GET["nbDoc"][$doc_id] = 1;
    }
  }
  CAppUI::requireModuleFile("dPcompteRendu", "print_docs");
  CApp::rip();
} 

$file = new CFile();
if ($compte_rendu->_id) {
  $compte_rendu->loadFile();
  $file = $compte_rendu->_ref_file;
}

// S'il n'y a pas de pdf ou qu'il est vide, on considère qu'il n'est pas généré
// pour le mode document
if (
    $mode != "modele" &&
    (((!$file || !$file->_id) && $first_time == 1 && !$compte_rendu->object_id) ||
    ($file && $file->_id && $first_time == 1 && (!is_file($file->_file_path) || file_get_contents($file->_file_path) == "")))
) {
  CAppUI::stepAjax(CAppUI::tr("CCompteRendu-no-pdf-generated"));
  return;
}
else if (
    $file && $file->_id && $first_time == 1 && is_file($file->_file_path) &&
    $compte_rendu->object_id && $mode == "doc" && file_get_contents($file->_file_path) != ''
) {
  // Rien
}
else {
  if ($mode == "modele") {
    switch ($type) {
      case "header":
      case "footer":
        $height = CValue::post("height", $compte_rendu->height);
        $content = $compte_rendu->loadHTMLcontent(
          $content,
          $mode,
          $margins,
          CCompteRendu::$fonts[$compte_rendu->font],
          $compte_rendu->size,
          true,
          $type, "", $height,
          "",
          ""
        );
        break;
      case "body":
      case "preface":
      case "ending":
        $header  = ""; $sizeheader = 0;
        $footer  = ""; $sizefooter = 0;
        $preface = "";
        $ending  = "";
        if ($header_id) {
          $component = new CCompteRendu;
          $component->load($header_id);
          $component->loadContent();
          $header = $component->_source;
          $sizeheader = $component->height;
        }
        if ($preface_id) {
          $component = new CCompteRendu;
          $component->load($preface_id);
          $component->loadContent();
          $preface = $component->_source;
        }
        if ($ending_id) {
          $component = new CCompteRendu;
          $component->load($ending_id);
          $component->loadContent();
          $ending = $component->_source;
        }
        if ($footer_id) {
          $component = new CCompteRendu;
          $component->load($footer_id);
          $component->loadContent();
          $footer = $component->_source;
          $sizefooter = $component->height;
        }
        
        $content = $compte_rendu->loadHTMLcontent(
          $content,
          $mode,
          $margins,
          CCompteRendu::$fonts[$compte_rendu->font],
          $compte_rendu->size,
          true,
          $type,
          $header,
          $sizeheader,
          $footer,
          $sizefooter,
          $preface,
          $ending
        );
    }
  }
  else {
    $content = $compte_rendu->loadHTMLcontent(
      $content,
      $mode,
      $margins,
      CCompteRendu::$fonts[$compte_rendu->font],
      $compte_rendu->size
    );
  }
  
  // Traitement du format de la page
  if ($page_format == "") {
    $page_width  = round((72 / 2.54) * $page_width, 2);
    $page_height = round((72 / 2.54) * $page_height, 2);
    $page_format = array(0, 0, $page_width, $page_height);
  }

  
  // Création du CFile si inexistant
  if (!$file->_id) {
    $file = new CFile();
    $file->setObject($compte_rendu);
    $file->file_type  = "application/pdf";
    $file->author_id = $user_id;
    $file->fillFields();
    $file->updateFormFields();
    $file->forceDir();
  }
  
  if ($file->_id && !file_exists($file->_file_path)) {
    $file->forceDir();
  }
  
  $file->file_name  = $compte_rendu->nom . ".pdf";
  
  $c1 = preg_replace("!\s!", '', $save_content);
  $c2 = preg_replace("!\s!", '', $compte_rendu->_source);
  
  // Si la source envoyée et celle présente en base sont différentes, on regénère le PDF
  // Suppression des espaces, tabulations, retours chariots et sauts de lignes pour effectuer le md5
  if ($compte_rendu->valide || md5($c1) != md5($c2) || !$file->_id || !file_exists($file->_file_path) || file_get_contents($file->_file_path) == "") {
    $htmltopdf = new CHtmlToPDF($compte_rendu->factory);
    $content = CCompteRendu::restoreId($content);
    $htmltopdf->generatePDF($content, 0, $compte_rendu, $file);
    $file->doc_size = filesize($file->_file_path);
  }
  
  // Il peut y avoir plusieurs cfiles pour un même compte-rendu, à cause 
  // de n requêtes simultanées pour la génération du pdf.
  // On supprime donc les autres cfiles.
  $compte_rendu->loadRefsFiles();
  $files = $compte_rendu->_ref_files;
  
  if ($file->_id) {
    unset($files[$file->_id]);
  }
  
  foreach ($files as $_file) {
    $_file->delete();
  }
  
  $file->store();
}

// Mise à jour de la date d'impression
if ($update_date_print) {
  $compte_rendu->date_print = "now";
  if ($msg = $compte_rendu->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
}

// Ajout de l'autoprint pour wkhtmltopdf (Cas où le pdf est déjà généré)
if ($compte_rendu->factory == "CWkHtmlToPDFConverter") {
  $content = file_get_contents($file->_file_path);

  if (!preg_match("#".CWkHtmlToPDFConverter::$to_autoprint."#", $content)) {
    $content = CWkHtmlToPDFConverter::addAutoPrint($content);
    file_put_contents($file->_file_path, $content);
    $file->doc_size = strlen($content);
    $file->store();
  }
}

if ($stream) {
  $file->streamFile();
  CApp::rip();
}

if ($write_page) {
  $file->loadNbPages();
  $smarty = new CSmartyDP;
  $smarty->assign("file_id", $file->_id);
  $smarty->assign("_nb_pages", $file->_nb_pages);
  $smarty->assign("print", $print);
  $smarty->assign("category_id", $compte_rendu->file_category_id);
  $smarty->display("inc_thumbnail.tpl");
}
