<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$praticien_id      = CValue::post("praticien_id");
$prescription_id   = CValue::post("prescription_id");
$libelle_protocole = CValue::post("libelle_protocole");
$perop             = CValue::post("perop", false);
$limit             = CValue::get("limit", 50);

$_tokens = explode(" ", $libelle_protocole);

$count_protocoles = 0;

// Chargement du praticien
$praticien = new CMediusers();
$praticien->load($praticien_id);
$praticien->loadRefFunction();

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);

// Valeur de recherche par defaut
if(!$prescription->_id){
	$prescription->type = CValue::post("type", "sejour");
	$prescription->object_class = "CSejour";
}

// Initialisations
$packs_praticien = array();
$packs_function = array();
$list = array();

// Chargement des protocoles
$protocole = new CPrescription();
$where = array();
$where[] = "prescription.praticien_id = '$praticien_id' OR prescription.function_id = '$praticien->function_id' OR prescription.group_id = '{$praticien->_ref_function->group_id}'";
$where["object_id"] = "IS NULL";
$where["object_class"] = " = '$prescription->object_class'";
$where["prescription.type"] = " = '$prescription->type'";
if($libelle_protocole){
	foreach($_tokens as $_token){
    $where[] = "prescription.libelle LIKE '%$_token%'";
	}
}

if($perop){
	$ljoin["prescription_line_medicament"] = "prescription_line_medicament.prescription_id = prescription.prescription_id";
	$ljoin["prescription_line_element"] = "prescription_line_element.prescription_id = prescription.prescription_id";
	$ljoin["prescription_line_mix"] = "prescription_line_mix.prescription_id = prescription.prescription_id";
  
	$where[] = "prescription_line_medicament.perop = '1' OR
	            prescription_line_element.perop = '1' OR 
							prescription_line_mix.perop = '1'";
	$group_by = "prescription.prescription_id";
  $protocoles = $protocole->loadList($where, "prescription.libelle", $limit, $group_by, $ljoin);
  $count_protocoles = $protocole->countList($where, $group_by, $ljoin);
} else {
	$protocoles = $protocole->loadList($where, "libelle", $limit);
	$count_protocoles = $protocole->countList($where);
}


// Chargement du nombre d'element par chapitre dans les protocoles
if (count($protocoles) <= CAppUI::conf("dPprescription CPrescription max_details_result"))  { 
  foreach($protocoles as $_protocole){
  	$_protocole->countLinesMedsElements();
				
  	foreach($_protocole->_counts_by_chapitre as $chapitre => $_count_chapitre){
  		if(!$_count_chapitre){
  			unset($_protocole->_counts_by_chapitre[$chapitre]);
  		}
  	}
  }
}

$list["prot"] = $protocoles; 

// Chargement des packs
$pack = new CPrescriptionProtocolePack();
$where = array();
$where[] = "praticien_id = '$praticien_id' OR function_id = '$praticien->function_id' OR group_id = '".CGroups::loadCurrent()->_id."'";
$where["object_class"] = " = '$prescription->object_class'";
if($libelle_protocole){
  foreach($_tokens as $_token){
    $where[] = "libelle LIKE '%$_token%'";
  }
}
$packs = !$perop ? $pack->loadList($where, "libelle", $limit) : array();

if (!$perop) {
  $count_protocoles += $pack->countList($where);
}

// Chargement du nombre d'element par chapitre dans les packs
foreach($packs as $_pack){
	$_pack->countElementsByChapitre();
}

$list["pack"] = $packs; 

// Tableau de tokens permettant de les mettre en evidence dans l'autocomplete
foreach($_tokens as $_token){
  $_token = strtoupper($_token);
  $token_search[] = $_token;
  $token_replace[] = "<em>".$_token."</em>";
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("protocoles", $protocoles);
$smarty->assign("packs", $packs);
$smarty->assign("nodebug", true);
$smarty->assign("list", $list);
$smarty->assign("token_search", $token_search);
$smarty->assign("token_replace", $token_replace);
$smarty->assign("limit", $limit);
$smarty->assign("count_protocoles", $count_protocoles);
$smarty->display("../../dPprescription/templates/inc_select_protocole.tpl");

?>