<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 23384 $
 */

$codable_class = CValue::get('codable_class', '');
$codable_id = CValue::get('codable_id');
$praticien_id = CValue::get('praticien_id');

$codage = new CCodageCCAM();

$codage->codable_class = $codable_class;
$codage->codable_id = $codable_id;
$codage->praticien_id = $praticien_id;
$codages = $codage->loadMatchingList('activite_anesth asc');

foreach ($codages as $_codage) {
  $_codage->canDo();

  if (!$_codage->_can->edit) {
    CAppUI::redirect("m=system&a=access_denied");
  }
  $_codage->loadPraticien()->loadRefFunction();
  $_codage->_ref_praticien->isAnesth();
  $_codage->loadActesCCAM();
  $_codage->checkRules();

  foreach ($_codage->_ref_actes_ccam as $_acte) {
    $_acte->getTarif();
  }

  // Chargement du codable et des actes possibles
  $_codage->loadCodable();
  $codable = $_codage->_ref_codable;
  $praticien = $_codage->_ref_praticien;
}

$codable->isCoded();
$codable->loadRefPatient();
$codable->loadRefPraticien();
$codable->loadExtCodesCCAM();
$codable->getAssociationCodesActes();
$codable->loadPossibleActes($praticien_id);

$praticien->loadRefFunction();
$praticien->isAnesth();

$list_activites = array();
foreach ($codable->_ext_codes_ccam as $_code) {
  foreach ($_code->activites as $_activite) {
    if ($praticien->_is_anesth && $_activite->numero == 4) {
      $list_activites[$_activite->numero] = true;
    }
    elseif (!$praticien->_is_anesth && $_activite->numero != 4) {
      $list_activites[$_activite->numero] = true;
    }
    else {
      $list_activites[$_activite->numero] = false;
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

//$smarty->assign("list_activites", $list_activites);
$smarty->assign("codages", $codages);
//$smarty->assign("codage", reset($codages));
$smarty->assign('subject', $codable);
$smarty->assign('praticien', $praticien);

$smarty->display("inc_edit_codages.tpl");