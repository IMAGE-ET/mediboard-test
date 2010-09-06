<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$compte_rendu_id = CValue::post("compte_rendu_id");
$content     = stripslashes(urldecode(CValue::post("content")));
$stream      = CValue::post("stream");
$compte_rendu = new CCompteRendu;
$compte_rendu->load($compte_rendu_id);
$margins     = CValue::post("margins", array($compte_rendu->margin_top,
                                             $compte_rendu->margin_right,
                                             $compte_rendu->margin_bottom,
                                             $compte_rendu->margin_left));
if ($textes_libres = CValue::post("texte_libre") && CAppUI::conf("dPcompteRendu CCompteRendu pdf_thumbnails") == 1) {
	$content = $compte_rendu->replaceFreeTextFields($content, $_POST["texte_libre"]);
}

$content = $compte_rendu->loadHTMLcontent($content, "doc",'','','','','',$margins);

$file = new CFile();
$file->setObject($compte_rendu);
$file->private = 0;
$file->file_name  = $compte_rendu->nom . ".pdf";
$file->file_type  = "application/pdf";
$file->fillFields();
$file->updateFormFields();
$file->forceDir();
$file->file_name  = $compte_rendu->nom . ".pdf";
$file->file_owner = CAppUI::$user->_id;

$htmltopdf = new CHtmlToPDF;
$htmltopdf->generatePDF($content, 0, $compte_rendu->_page_format, $compte_rendu->_orientation, $file);

$file->file_size = filesize($file->_file_path);
$msg = $file->store();

CAppUI::displayMsg($msg, "CCompteRendu-msg-create");
echo CAppUI::getMsg();

if ($stream) {
	$htmltopdf->dompdf->stream($file->file_name, array("Attachment" => 1));
}
CApp::rip();
?>