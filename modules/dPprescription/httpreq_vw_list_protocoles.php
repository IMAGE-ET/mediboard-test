<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$praticien_id = CValue::get("praticien_id");
$function_id  = CValue::get("function_id");
$group_id     = CValue::get("group_id");
$protocoleSel_id = CValue::get("protocoleSel_id");
$code_cis = CValue::get("code_cis");
$element_prescription_id = CValue::get("element_prescription_id");
$libelle_protocole = CValue::get("libelle_protocole");

$is_praticien = CAppUI::$user->isPraticien();
$search = false;


// Recherche suivant des critères
if($code_cis || $element_prescription_id || $libelle_protocole){
  $group_id = CGroups::loadCurrent()->_id;

	$search = true;
	$protocole = new CPrescription();
	$where = array();
	$ljoin = array();
	
	$where["object_id"] = " IS NULL";
	if($libelle_protocole){
	  $where["prescription.libelle"] = " LIKE '%$libelle_protocole%'";
  }

  if($element_prescription_id){
  	$ljoin["prescription_line_element"] = "prescription_line_element.prescription_id = prescription.prescription_id";
		$where["prescription_line_element.element_prescription_id"] = " = '$element_prescription_id'";
  }
	
	if($code_cis){
	  $ljoin["prescription_line_medicament"] = "prescription_line_medicament.prescription_id = prescription.prescription_id";
		$ljoin["prescription_line_mix"] = "prescription_line_mix.prescription_id = prescription.prescription_id";
		$ljoin["prescription_line_mix_item"] = "prescription_line_mix_item.prescription_line_mix_id = prescription_line_mix.prescription_line_mix_id";
    $where[] = "prescription_line_medicament.code_cis = '$code_cis' OR prescription_line_mix_item.code_cis = '$code_cis'";
	}
	
	// Filtre par etablissement
	$ljoin["users_mediboard"] = "prescription.praticien_id = users_mediboard.user_id";
	$ljoin["functions_mediboard"] = "prescription.function_id = functions_mediboard.function_id OR users_mediboard.function_id = functions_mediboard.function_id";
  $where[] = "prescription.group_id = '$group_id' OR functions_mediboard.group_id = '$group_id'";

	$_protocoles = $protocole->loadList($where, null, null, null, $ljoin);
	
	$protocoles = array(
      "prat"  => array(), 
      "func"  => array(),
      "group" => array()
    );
		
	foreach ($_protocoles as $_protocole) {
		$_protocole->loadRefFunction();
		$_protocole->loadRefGroup();
    
		$owner = $_protocole->praticien_id ? "prat" : ($_protocole->function_id ? "func" : "group");
		$protocoles[$owner][$_protocole->object_class][$_protocole->type][$_protocole->_id] = $_protocole;	 
	}
} 
// Recherche des protocoles suivant le propriétaire
else {
	$protocole = new CPrescription();
	
	if(!$function_id && !$praticien_id && $protocoleSel_id){
	  $protocole->load($protocoleSel_id);
	  $praticien_id = $protocole->praticien_id; 
	  $function_id = $protocole->function_id;
	}
	
	$protocoles = CPrescription::getAllProtocolesFor($praticien_id, $function_id, $group_id);	
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("protocoles"      , $protocoles);
$smarty->assign("protocoleSel_id" , $protocoleSel_id);
$smarty->assign("praticien_id"    , $praticien_id);
$smarty->assign("function_id"     , $function_id);
$smarty->assign("group_id"        , $group_id);
$smarty->assign("is_praticien"    , $is_praticien);
$smarty->assign("search"          , $search);
$smarty->display("inc_vw_list_protocoles.tpl");

?>