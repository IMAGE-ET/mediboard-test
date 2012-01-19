<?php /* $Id: do_items_liaisons_aed.php $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$liaisons_j = CValue::post("liaisons_j");
$liaisons_p = CValue::post("liaisons_p");

if (is_array($liaisons_p)) {
  foreach ($liaisons_p as $liaison_id=>$quantite) {
    $item_liaison = new CItemLiaison;
    $item_liaison->load($liaison_id);
    $item_liaison->quantite = $quantite;
    $item_liaison->store();
  }
}

if (is_array($liaisons_j)) {
  foreach ($liaisons_j as $affectation_id => $by_affectation) {
    foreach ($by_affectation as $prestation_id => $by_date) {
      foreach ($by_date as $date=>$liaison) {
        $souhait_id = null;
        $realise_id = null;
        $item_liaison = new CItemLiaison;
        
        if (isset($liaison['souhait'])) {
          if (isset($liaison['souhait']['new'])) {
            $souhait_id = $liaison['souhait']['new'];
          }
          else {
            $souhait_id = reset($liaison['souhait']);
            $item_liaison->load(reset(array_keys($liaison['souhait'])));
          }
        }
        if (isset($liaison['realise'])) {
          if (isset($liaison['souhait']['new'])) {
            $realise_id = $liaison['realise']['new'];
          }
          else {
            $realise_id = reset($liaison['realise']);
            if (!$item_liaison->_id) {
              $item_liaison->load(reset(array_keys($liaison['realise'])));
            }
          }
        }
        if (!$item_liaison->_id) {
          $item_liaison->date = $date;
          $item_liaison->affectation_id = $affectation_id;
        }
        
        $item_liaison->item_prestation_id = $souhait_id;
        $item_liaison->item_prestation_realise_id = $realise_id;
        mbLog($item_liaison->store());
      }
    }
  }
}

CAppUI::setMsg("CItemLiaison-modified_selection");

echo CAppUI::getMsg();
CApp::rip();
?>