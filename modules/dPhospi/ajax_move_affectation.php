<?php /* $Id: ajax_move_affectation.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$affectation_id = CValue::get("affectation_id");
$lit_id         = CValue::get("lit_id");
$sejour_id      = CValue::get("sejour_id");

$affectation = new CAffectation;

if ($affectation_id) {
  $affectation->load($affectation_id);
  
  // On déplace l'affectation parente si nécessaire
  if (null != $affectation_id = $affectation->parent_affectation_id) {
    $affectation = new CAffectation;
    $affectation->load($affectation_id);
  }
}
else {
  $affectation->sejour_id = $sejour_id;
  $sejour = new CSejour;
  $sejour->load($sejour_id);
  $affectation->entree = $sejour->entree;
  $affectation->sortie = $sejour->sortie;
}

$affectation->lit_id = $lit_id;

// Si l'affectation est un blocage, il faut vider le champ sejour_id
if ($affectation->sejour_id == 0) {
  $affectation->sejour_id = "";
}

if ($msg = $affectation->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}

$affectations_enfant = $affectation->loadBackRefs("affectations_enfant");
foreach ($affectations_enfant as $_affectation) {
  $_affectation->lit_id = $lit_id;
  if ($msg = $_affectation->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
}

// Niveaux de prestations réalisées à créer
// pour une nouvelle affectation (par rapport aux niveaux de prestations du lit)
if (!$affectation_id && isset($sejour)) {
  $lit = new CLit;
  $lit->load($lit_id);
  $liaisons_lit = $lit->loadRefsLiaisonsItems();
  CMbObject::massLoadFwdRef($liaisons_lit, "item_prestation_id");
  
  foreach ($liaisons_lit as $_liaison) {
    // Chercher une éventuelle liaison, sinon la créer.
    
    $_item = $_liaison->loadRefItemPrestation();
    
    $item_liaison = new CItemLiaison;
    $where = array();
    $ljoin = array();
    
    $where["sejour_id"] = " = '$sejour->_id'";
    $where["item_prestation.object_class"] = " = 'CPrestationJournaliere'";
    $where["item_prestation.object_id"] = "= '$_item->object_id'";
    $ljoin["item_prestation"] = "item_prestation.item_prestation_id = item_liaison.item_prestation_id";
    
    $item_liaison->loadObject($where, null, null, $ljoin);
    
    if (!$item_liaison->_id) {
      $item_liaison = new CItemLiaison;
      $item_liaison->sejour_id = $sejour->_id;
      $item_liaison->date = mbDate($sejour->entree);
      $item_liaison->quantite = 0;
    }
    
    $item_liaison->item_prestation_realise_id = $_liaison->item_prestation_id;
    
    if ($msg = $item_liaison->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
    }
  }
}

echo CAppUI::getMsg();
?>