<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Si on a un file_id, on stream le pdf
if ($file_id = CValue::post("file_id")) {
  $file = new CFile;
  $file->load($file_id);
  
  // Mise à jour de la date d'impression
  $cr = new CCompteRendu;
  $cr->load($file->object_id);
  $cr->loadContent();
  $cr->date_print = "now";
  
  if ($msg = $cr->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
  $file->streamFile();
  CApp::rip();
}

$compte_rendu_id = CValue::post("compte_rendu_id");
$stream      = CValue::post("stream");
$print_to_server = CValue::post("print_to_server");
$compte_rendu = new CCompteRendu;
$compte_rendu->load($compte_rendu_id);
$compte_rendu->loadContent(1);
$content = $compte_rendu->_source;
$margins = array($compte_rendu->margin_top,
                 $compte_rendu->margin_right,
                 $compte_rendu->margin_bottom,
                 $compte_rendu->margin_left);

$content = $compte_rendu->loadHTMLcontent($content, "", $margins, CCompteRendu::$fonts[$compte_rendu->font], $compte_rendu->size);

$file = new CFile();
$file->setObject($compte_rendu);
$file->private = 0;
$file->file_name  = $compte_rendu->nom . ".pdf";
$file->file_type  = "application/pdf";
$file->fillFields();
$file->updateFormFields();
$file->forceDir();
$file->file_name  = $compte_rendu->nom . ".pdf";
$file->author_id = CAppUI::$user->_id;

$htmltopdf = new CHtmlToPDF;
$htmltopdf->generatePDF($content, 0, $compte_rendu->_page_format, @isset(CCompteRendu::$_page_formats[$compte_rendu->_page_format]) ? $compte_rendu->_orientation : null, $file);

$file->file_size = filesize($file->_file_path);
$msg = $file->store();

CAppUI::displayMsg($msg, "CCompteRendu-msg-create");
echo CAppUI::getMsg();

// Un callback pour le stream du pdf
if ($stream) {
  echo "\n<script type=\"text/javascript\">streamPDF($file->_id)</script>";
}

// Un callback pour l'impression server side
if ($print_to_server) {
  // Mise à jour de la date d'impression
  $compte_rendu->date_print = "now";
  if ($msg = $compte_rendu->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
  
  echo "\n<script type=\"text/javascript\">printToServer($file->_id)</script>";
}

CApp::rip();
?>