<?php 

/**
 * $Id$
 *  
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$default_disposition = CValue::get("disposition", CAppUI::pref("mozaic_disposition", "2x2"));

$context = CValue::get("context_guid");

$doc = new CFile();
$doc->canDo();
if (!$doc->_can->edit) {
  CAppUI::stepAjax("pas le droit de créer un CFile", UI_MSG_ERROR);
}

if (!$context) {
  CAppUI::stepAjax("no_context_provided", UI_MSG_ERROR);
}
$context = CMbObject::loadFromGuid($context);
if (!$context->_id) {
  CAppUI::stepAjax("unexisting", UI_MSG_ERROR);
}
$context->canDo();
if (!$context->_can->read) {
  CAppUI::stepAjax("No right", UI_MSG_ERROR);
}

switch ($context->_class) {
  case 'CPatient':
    $patient = $context;
    break;

  case 'CSejour':
    /** @var CSejour $context */
    $patient = $context->loadRefPatient();
    break;

  case 'CConsultation':
  case 'CConsultationAnesth':
    /** @var CConsultation $context */
    $patient = $context->loadRefPatient();
    break;

  case 'COperation':
    /** @var COperation $context */
    $patient = $context->loadRefPatient();
    break;

  default:
    $patient = new CPatient();
    break;
}

if (!$patient->_id) {
  CAppUI::stepAjax("CPatient-none", UI_MSG_ERROR);
}

$patient->loadRefsFiles();
foreach ($patient->_ref_files as $_key => $_file) {
  $right = $_file->canDo();
  if (!$_file->isImage() || !$_file->_can->read) {
    unset($patient->_ref_files[$_key]);
    continue;
  }
}

/** @var CConsultation[] $consults */
$consults = $patient->loadRefsConsultations();
CMbObject::filterByPerm($consults, PERM_READ);
foreach ($consults as $_consult) {
  $_consult->loadRefsFiles();
  foreach ($_consult->_ref_files as $_key => $_file) {
    $right = $_file->canDo();
    if (!$_file->isImage() || !$_file->_can->read) {
      unset($_consult->_ref_files[$_key]);
      continue;
    }
  }
}

$sejours  = $patient->loadRefsSejours();
CMbObject::filterByPerm($sejours, PERM_READ);
foreach ($sejours as $_sejour) {
  $_sejour->loadRefsFiles();
  foreach ($_sejour->_ref_files as $_key => $_file) {
    $right = $_file->canDo();
    if (!$_file->isImage() || !$_file->_can->read) {
      unset($_sejour->_ref_files[$_key]);
      continue;
    }
  }

  $operations = $_sejour->loadRefsOperations();
  CMbObject::filterByPerm($operations);
  foreach ($operations as $_op) {
    $_op->loadRefsFiles();
    foreach ($_op->_ref_files as $_key => $_file) {
      $right = $_file->canDo();
      if (!$_file->isImage() || !$_file->_can->read) {
        unset($_op->_ref_files[$_key]);
        continue;
      }
    }
  }
}

// file categories
$category = new CFilesCategory();
$categories = $category->loadListWithPerms(PERM_EDIT);

$matrices = array();
$matrices["1x2"] = array("line" => 2, "col"=> 1);
$matrices["2x1"] = array("line" => 1, "col"=> 2);
$matrices["2x2"] = array("line" => 2, "col"=> 2);
$matrices["2x3"] = array("line" => 3, "col"=> 2);
$matrices["3x2"] = array("line" => 2, "col"=> 3);
$matrices["3x3"] = array("line" => 3, "col"=> 3);

$smarty = new CSmartyDP();
$smarty->assign("patient", $patient);
$smarty->assign("context", $context);
$smarty->assign("matrices", $matrices);
$smarty->assign("categories", $categories);
$smarty->assign("default_disposition", $default_disposition);
$smarty->display("inc_img_to_document.tpl");