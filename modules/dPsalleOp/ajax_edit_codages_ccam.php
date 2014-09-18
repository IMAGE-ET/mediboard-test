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

$codage_id = CValue::get("codage_id");

$codage = new CCodageCCAM();
$codage->load($codage_id);
$codage->canDo();

if (!$codage->_can->edit) {
  CAppUI::redirect("m=system&a=access_denied");
}
$codage->loadPraticien()->loadRefFunction();
$codage->_ref_praticien->isAnesth();
$codage->loadActesCCAM();
$codage->checkRules();

foreach ($codage->_ref_actes_ccam as $_acte) {
  $_acte->getTarif();
}

// Chargement du codable et des actes possibles
$codage->loadCodable();

$codable = $codage->_ref_codable;
$codable->isCoded();
$codable->loadRefPatient();
$codable->loadRefPraticien();
$codable->loadExtCodesCCAM();
$codable->getAssociationCodesActes();
$codable->loadPossibleActes($codage->praticien_id);

$list_activites = array();
foreach ($codable->_ext_codes_ccam as $_code) {
  foreach ($_code->activites as $_activite) {
    if ($codage->_ref_praticien->_is_anesth && $_activite->numero == 4) {
      $list_activites[$_activite->numero] = true;

    }
    elseif (!$codage->_ref_praticien->_is_anesth && $_activite->numero != 4) {
      $list_activites[$_activite->numero] = true;
    }
    else {
      $list_activites[$_activite->numero] = false;
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("list_activites", $list_activites);
$smarty->assign("codage", $codage);

$smarty->display("inc_edit_codages.tpl");