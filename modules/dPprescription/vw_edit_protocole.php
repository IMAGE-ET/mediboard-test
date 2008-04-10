<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$praticien_id = mbGetValueFromGetOrSession("praticien_id");
$protocole_id = mbGetValueFromGet("prescription_id");

$protocoles = array();
$protocole = new CPrescription();
$listFavoris = array();

// Chargement de la liste des praticiens
$praticien = new CMediusers();
$praticiens = $praticien->loadPraticiens();
$praticien->load($praticien_id);

// Chargement des protocoles de prescription du praticien selectionne
if($praticien_id){
  $prescription = new CPrescription();
  $where["praticien_id"] = " = '$praticien_id'";
  $where["object_id"] = "IS NULL";
  $tabProtocoles = $prescription->loadList($where);
  foreach($tabProtocoles as $_protocole){
  	$protocoles[$_protocole->object_class][$_protocole->_id] = $_protocole;
  }
}

if($protocole_id){
	$protocole = new CPrescription();
	$protocole->load($protocole_id);
	$protocole->loadRefsLines();
	$protocole->loadRefsLinesElementByCat();
}

if($praticien_id){
  $listFavoris = CPrescription::getFavorisPraticien($praticien_id);
}

$contexteType = array();
$contexteType["CConsultation"][] = "externe";
$contexteType["CSejour"][] = "pre_admission";
$contexteType["CSejour"][] = "sortie";
$contexteType["CSejour"][] = "sejour";


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("contexteType", $contexteType);
$smarty->assign("praticien_id", $praticien_id);
$smarty->assign("praticiens" , $praticiens);
$smarty->assign("praticien"  , $praticien);
$smarty->assign("protocoles" , $protocoles);
$smarty->assign("protocole"  , $protocole);
$smarty->assign("listFavoris", $listFavoris);
$smarty->assign("protocoleSel_id", "");

$smarty->display("vw_edit_protocole.tpl");

?>