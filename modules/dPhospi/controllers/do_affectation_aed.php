<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPhospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */
$_lock_all_lits          = CValue::post("_lock_all_lits");
$_lock_all_lits_urgences = CValue::post("_lock_all_lits_urgences");
$lit_id                  = CValue::post("lit_id");
$entree                  = CValue::post("entree");
$sortie                  = CValue::post("sortie");
$function_id             = CValue::post("function_id");

if ($_lock_all_lits || $_lock_all_lits_urgences) {
  /** @var CLit $lit */
  $lit = new CLit();
  $lit = $lit->load($lit_id);
  $lit->loadRefChambre()->loadRefService()->loadRefsChambres();
  
  foreach ($lit->_ref_chambre->_ref_service->_ref_chambres as $chambre) {
    $chambre->loadRefsLits();
    foreach ($chambre->_ref_lits as $lit) {
      $affectation = new CAffectation();
      $affectation->lit_id = $lit->_id;
      $affectation->entree = $entree;
      $affectation->sortie = $sortie;
      if ($_lock_all_lits_urgences) {
        $affectation->function_id = $function_id;
      }
      if ($msg = $affectation->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }
    }
  }

  echo CAppUI::getMsg();
  CApp::rip();
}
else {
  $tolerance      = CAppUI::conf("dPhospi CAffectation create_affectation_tolerance", CGroups::loadCurrent());
  $sejour_id      = CValue::post("sejour_id");
  $affectation_id = CValue::post("affectation_id");

  //Si on est en création d'afectation et qu'il y a un sejour_id
  if (!$affectation_id && $sejour_id) {
    $sejour = new CSejour();
    $sejour->load($sejour_id);
    $curr_affectation = $sejour->loadRefCurrAffectation();
    //On modifie au lieu de créer une affectation si l'afectation courante ne dépasse pas la tolérance
    if ($curr_affectation && $curr_affectation->_id) {
      if (CMbDT::addDateTime("00:$tolerance:00", $curr_affectation->entree) > $entree) {
        $_POST["affectation_id"] = $curr_affectation->_id;
      }
    }
  }

  $do = new CDoObjectAddEdit("CAffectation", "affectation_id");
  $do->doIt();
}