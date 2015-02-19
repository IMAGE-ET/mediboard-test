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
$codable_id    = CValue::get('codable_id');
$praticien_id  = CValue::get('praticien_id');
$date          = CValue::get('date');

$codage = new CCodageCCAM();

$codage->codable_class = $codable_class;
$codage->codable_id = $codable_id;
$codage->praticien_id = $praticien_id;
if ($date) {
  $codage->date = $date;
}
$codages = $codage->loadMatchingList('activite_anesth desc');

foreach ($codages as $_codage) {
  $_codage->canDo();

  if (!$_codage->_can->edit) {
    CAppUI::redirect("m=system&a=access_denied");
  }
  $_codage->loadPraticien()->loadRefFunction();
  $_codage->_ref_praticien->isAnesth();
  $_codage->loadActesCCAM();
  $_codage->getTarifTotal();
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
//$codable->getAssociationCodesActes();
/* On charge les codages ccam du séjour en lui précisant une date pour ne pas qu'il charge tous les codages liés au sejour */
if ($codable->_class == 'CSejour') {
  $codable->loadRefsCodagesCCAM($date, $date);
}
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

    if ($codable->_class == 'CSejour') {
      foreach ($_activite->phases as $_phase) {
        $_acte =& $_phase->_connected_acte;
        /* On met la date d'execution des actes non cotés à la date du codage pour les séjours */
        if (!$_acte->_id) {
          $_acte->execution = CMbDT::format($codage->date, '%Y-%m-%d ') . CMbDT::format(CMbDT::dateTime(), '%T');
        }
      }
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