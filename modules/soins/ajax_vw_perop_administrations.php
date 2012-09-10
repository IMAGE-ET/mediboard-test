<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id = CValue::get("prescription_id");

// Chargement des administrations
$administration = new CAdministration();
$administrations = array();

$where = array();
$where["prescription.prescription_id"] = " = '$prescription_id'";

// CPrescriptionLineMedicament
$ljoin = array(
  "prescription_line_medicament" => "prescription_line_medicament.prescription_line_medicament_id = administration.object_id
                                       AND administration.object_class = 'CPrescriptionLineMedicament'
                                       AND prescription_line_medicament.perop = '1'",
  "prescription"                 => "prescription_line_medicament.prescription_id = prescription.prescription_id",
);
$administrations = array_merge($administrations, $administration->loadList($where, null, null, null, $ljoin));

// CPrescriptionLineElement
$ljoin = array(
  "prescription_line_element"    => "prescription_line_element.prescription_line_element_id = administration.object_id 
                                       AND administration.object_class = 'CPrescriptionLineElement'
                                       AND prescription_line_element.perop = '1'",
  "prescription"                 => "prescription_line_element.prescription_id = prescription.prescription_id",
);
$administrations = array_merge($administrations, $administration->loadList($where, null, null, null, $ljoin));

// CPrescriptionLineMixItem
$ljoin = array(
  "prescription_line_mix_item"   => "prescription_line_mix_item.prescription_line_mix_item_id = administration.object_id 
                                       AND administration.object_class = 'CPrescriptionLineMixItem'",
  "prescription_line_mix"        => "prescription_line_mix_item.prescription_line_mix_id = prescription_line_mix.prescription_line_mix_id
                                       AND prescription_line_mix.perop = '1'",
  "prescription"                 => "prescription_line_mix.prescription_id = prescription.prescription_id",
);
$administrations = array_merge($administrations, $administration->loadList($where, null, null, null, $ljoin));

$administrations = CMbObject::naturalSort($administrations, array("dateTime"));

foreach ($administrations as $_adm) {
  $_adm->loadTargetObject();
  
  if ($_adm->_ref_object instanceof CPrescriptionLineMedicament || $_adm->_ref_object instanceof CPrescriptionLineMixItem) {
    $_produit = $_adm->_ref_object->_ref_produit;
    
    $_produit->loadUnitePresentation();
    $_produit->loadRapportUnitePriseByCIS();
    $_produit->updateRatioMassique();
    
    if ($_produit->_ratio_mg) {
      $_adm->_quantite_mg = $_adm->quantite / $_produit->_ratio_mg;
    }
  }
}

$smarty = new CSmartyDP();
$smarty->assign("administrations", $administrations);
$smarty->display("inc_vw_perop_administrations.tpl");

?>