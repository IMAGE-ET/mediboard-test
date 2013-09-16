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
$type_print = CValue::get("type_print", "bvr");

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
  $facture_pdf = new CEditPdf();
  $facture_pdf->factures = $factures;
  $pdf = "";
  if ($type_print == "bvr") {
    $pdf = $facture_pdf->editFactureBVR(false, "S");
  }

  if ($type_print == "justif") {
    $pdf = $facture_pdf->editJustificatif(false, "S");
  }

  $printer = new CPrinter();
  $printer->function_id = CMediusers::get()->function_id;
  $printer->label = $type_print;
  $printer->loadMatchingObject();

  $file = new CFile();
  $file_path = tempnam("tmp", "facture");
  $file->_file_path = $file_path;
  file_put_contents($file_path, $pdf);

  $printer->loadRefSource()->sendDocument($file);
  unlink($file_path);
}