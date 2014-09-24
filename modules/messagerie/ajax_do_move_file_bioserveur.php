<?php 

/**
 * $Id$
 *  
 * @category Messagerie
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

//get
$document_guid = CValue::get("document_guid");
$patient_id = CValue::get("patient_id");

/** @var CDocumentExterne $document */
$document = CMbObject::loadFromGuid($document_guid);
if (!$document->_id) {
  CAppUI::stepAjax("PB");
}

$account = $document->loadRefAccount();
$praticien = $account->loadRefMediuser();

$file = $document->loadRefFile(true);

$cat = new CFilesCategory();
$cats = $cat->loadListWithPerms();

if (!$file->_id) {
  CAppUI::stepAjax("CBioServeurAccount-msg-no_file_id_spectified_for_moving", UI_MSG_ERROR);
}
$file->loadTargetObject();


//finding patient
$patient = $document->findPatient();

//smarty
$smarty = new CSmartyDP();
$smarty->assign("file", $file);
$smarty->assign("file_categories", $cats);
$smarty->assign("praticien", $praticien);
$smarty->assign("document", $document);
$smarty->assign("guessing_date", $document->document_date);
$smarty->assign("patient", $patient);
$smarty->display("inc_move_file.tpl");