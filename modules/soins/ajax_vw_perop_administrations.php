<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id   = CValue::get("prescription_id");

  
$administration = new CAdministration();
$ljoin["prescription_line_medicament"] = "prescription_line_medicament.prescription_line_medicament_id = administration.object_id AND administration.object_class = 'CPrescriptionLineMedicament'";
$ljoin["prescription_line_element"]    = "prescription_line_element.prescription_line_element_id = administration.object_id AND administration.object_class = 'CPrescriptionLineElement'";
$ljoin["prescription_line_mix_item"]   = "prescription_line_mix_item.prescription_line_mix_item_id = administration.object_id AND administration.object_class = 'CPrescriptionLineMixItem'";
$ljoin["prescription_line_mix"]        = "prescription_line_mix.prescription_line_mix_id = prescription_line_mix_item.prescription_line_mix_id";
                                                                                       
$ljoin["prescription"] = "(prescription_line_medicament.prescription_id = prescription.prescription_id) OR
                          (prescription_line_element.prescription_id = prescription.prescription_id) OR
                          (prescription_line_mix.prescription_id = prescription.prescription_id)";

$where["prescription.prescription_id"] = " = '$prescription_id'";

$where[] = "prescription_line_medicament.perop = '1' OR 
            prescription_line_element.perop = '1' OR
            prescription_line_mix.perop = '1'";
    
$order = "dateTime ASC";		
$administrations = $administration->loadList($where, $order, null, null, $ljoin);

foreach($administrations as $_adm){
	$_adm->loadTargetObject();
	if($_adm->_ref_object instanceof CPrescriptionLineMedicament || $_adm->_ref_object instanceof CPrescriptionLineMixItem){
		$_adm->_ref_object->_ref_produit->loadUnitePresentation();
    $_adm->_ref_object->_ref_produit->loadRapportUnitePriseByCIS();
    $_adm->_ref_object->_ref_produit->updateRatioMassique();
    if($_adm->_ref_object->_ref_produit->_ratio_mg){
      $_adm->_quantite_mg = $_adm->quantite / $_adm->_ref_object->_ref_produit->_ratio_mg;
    }
	}
}

$smarty = new CSmartyDP();
$smarty->assign("administrations", $administrations);
$smarty->display("inc_vw_perop_administrations.tpl");

?>