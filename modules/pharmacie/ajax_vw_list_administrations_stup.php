<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$date_min = CValue::get("date_min");
$date_max = CValue::get("date_max");
$print = CValue::get("print");

$min = "$date_min 00:00:00";
$max = "$date_max 23:59:59";

// Recherche des administrations de stupefiants dans les dates donnees
$administration = new CAdministration();
$ljoin["prescription_line_medicament"] = "prescription_line_medicament.prescription_line_medicament_id = administration.object_id AND 
                                          administration.object_class = 'CPrescriptionLineMedicament'";
$ljoin["prescription_line_mix_item"] = "prescription_line_mix_item.prescription_line_mix_item_id = administration.object_id AND
                                        administration.object_class = 'CPrescriptionLineMixItem'";																					
																		
$where = array();
$where["dateTime"] = " BETWEEN '$min' AND '$max'";
$where[] = "prescription_line_medicament.stupefiant = '1' OR prescription_line_mix_item.stupefiant = '1'";
$administrations = $administration->loadList($where, null, null, null, $ljoin);

foreach($administrations as $_administration){
  $_administration->loadTargetObject();	
	if($_administration->_ref_object instanceof CPrescriptionLineMedicament){
		$prescription =& $_administration->_ref_object->_ref_prescription;
	} else {
		$prescription =& $_administration->_ref_object->_ref_prescription_line_mix->_ref_prescription;
	}
	$prescription->_ref_object->loadRefPatient();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("administrations", $administrations);
$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);
$smarty->assign("print", $print);
$smarty->display('inc_vw_list_administrations_stup.tpl');

?>