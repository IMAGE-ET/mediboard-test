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

$_tokens = explode(" ", $libelle_protocole);

// Chargement du praticien
$praticien = new CMediusers();
$praticien->load($praticien_id);
$praticien->loadRefFunction();

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);

// Valeur de recherche par defaut
if(!$prescription->_id){
	$prescription->type = "sejour";
	$prescription->object_class = "CSejour";
}

// Initialisations
$packs_praticien = array();
$packs_function = array();
$list = array();

// Chargement des protocoles
$protocole = new CPrescription();
$where = array();
$where[] = "praticien_id = '$praticien_id' OR function_id = '$praticien->function_id' OR group_id = '{$praticien->_ref_function->group_id}'";
$where["object_id"] = "IS NULL";
$where["object_class"] = " = '$prescription->object_class'";
$where["type"] = " = '$prescription->type'";
if($libelle_protocole){
	foreach($_tokens as $_token){
    $where[] = "libelle LIKE '%$_token%'";
	}
}
$protocoles = $protocole->loadList($where, "libelle");

// Chargement du nombre d'element par chapitre dans les protocoles
foreach($protocoles as $_protocole){
	$_protocole->countLinesMedsElements();
	foreach($_protocole->_counts_by_chapitre as $chapitre => $_count_chapitre){
		if(!$_count_chapitre){
			unset($_protocole->_counts_by_chapitre[$chapitre]);
		}
	}
}

$list["prot"] = $protocoles; 

// Chargement des packs
$pack = new CPrescriptionProtocolePack();
$where = array();
$where[] = "praticien_id = '$praticien_id' OR function_id = '$praticien->function_id'";
$where["object_class"] = " = '$prescription->object_class'";
if($libelle_protocole){
  foreach($_tokens as $_token){
    $where[] = "libelle LIKE '%$_token%'";
  }
}
$packs = $pack->loadList($where, "libelle");

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
$smarty->display("../../dPprescription/templates/inc_select_protocole.tpl");

?>