<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */
CCanDo::checkEdit();
$date       = CValue::get("date", CMbDT::date());

$facture = new CFactureEtablissement();
$facture->cloture = $date;
$facture->definitive = 0;
$factures = $facture->loadMatchingList();

foreach ($factures as $_facture) {
  $_facture->definitive = 1;
  if ($msg = $_facture->store()) {
    CAppUI::setMsg(CMbDT::dateTime().$msg, UI_MSG_ERROR);
  }
}

$facture = new CFactureEtablissement();
$facture->cloture = $date;
$facture->definitive = 1;
$factures = $facture->loadMatchingList();


if (count($factures)) {
  $user = CMediusers::get();
  $printer_bvr = new CPrinter();
  $printer_bvr->function_id = $user->function_id;
  $printer_bvr->label = "bvr";
  $printer_bvr->loadMatchingObject();

  $printer_justif = new CPrinter();
  $printer_justif->function_id = $user->function_id;
  $printer_justif->label = "justif";
  $printer_justif->loadMatchingObject();

  $file = new CFile();

  foreach ($factures as $facture) {
    $facture_pdf = new CEditPdf();
    $facture_pdf->factures = array($facture);
    $pdf = "";
    $pdf = $facture_pdf->editFactureBVR(false, "S");
    $file_path = tempnam("tmp", "facture");
    $file->_file_path = $file_path;
    file_put_contents($file_path, $pdf);
    $printer_bvr->loadRefSource()->sendDocument($file);
    unlink($file_path);

    $pdf = "";
    $pdf = $facture_pdf->editJustificatif(false, "S");
    $file_path = tempnam("tmp", "facture");
    $file->_file_path = $file_path;
    file_put_contents($file_path, $pdf);
    $printer_justif->loadRefSource()->sendDocument($file);
    unlink($file_path);
  }
}