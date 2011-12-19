<?php /* $Id: do_items_liaisons_aed.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prestations_j = CValue::post("prestations_j");
$prestations_p = CValue::post("prestations_p");

// Regénération des liaisons pour les prestations journalières
if (is_array($prestations_j)) {
  foreach ($prestations_j as $_prestation) {
    foreach ($_prestation as $_affectation_id => $_affectation) {    
      $affect = new CAffectation;
      $affect->load($_affectation_id);
      $items_liaisons = $affect->loadBackRefs("items_liaisons");
      foreach ($items_liaisons as $_item) {
        $_item->delete();
      }
    }
  }
  
  foreach ($prestations_j as $_prestation) {
    foreach ($_prestation as $_affectation_id => $_affectation) {
      foreach ($_affectation as $date => $data) {
        if ($data["item_prestation_id"]) {
          $item_liaison = new CItemLiaison;
          $item_liaison->affectation_id = $_affectation_id;
          $item_liaison->item_prestation_id = $data["item_prestation_id"];
          $item_liaison->item_prestation_realise_id = $data["item_prestation_realise_id"];
          $item_liaison->date = $date;
          $item_liaison->store();
        }
      }
    }
  }
}

// Génération des liaisons pour les prestations ponctuelles
// Pas de suppression car déjà faite dans le if précédent
if (is_array($prestations_p)) {
  foreach ($prestations_p as $_prestation) {
    foreach ($_prestation as $_affectation_id => $_affectation) {
      foreach ($_affectation as $date => $items) {
        foreach ($items as $item_id=>$quantite) {
          if ($quantite > 0) {
            $item_liaison = new CItemLiaison;
            $item_liaison->affectation_id = $_affectation_id;
            $item_liaison->item_prestation_id = $item_id;
            $item_liaison->quantite = $quantite;
            $item_liaison->date = $date;
            $item_liaison->store();
          }
        }
      }
    }
  }
}

CAppUI::setMsg("CItemLiaison-modified_selection");

echo CAppUI::getMsg();
CApp::rip();
?>