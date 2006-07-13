<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {			// lock out users that do not have at least readPermission on this module
	$AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPcabinet', 'consultation') );
require_once( $AppUI->getModuleClass('dPcabinet', 'plageconsult') );

$deb = mbGetValueFromGetOrSession("deb", mbDate());
$fin = mbGetValueFromGetOrSession("fin", mbDate());
$chir = mbGetValueFromGetOrSession("chir");

// Liste des praticiens
if(!$chir) {
  $mediusers = new CMediusers();
  $listChir = $mediusers->loadPraticiens(PERM_EDIT);
  $inArray = array();
  foreach($listChir as $key => $value) {
    $inArray[] = $key;
    $in = "IN (".implode(", ", $inArray).")";
  }
} else {
  $in = "= '$chir'";
}

// On selectionne les plages
$listPlage = new CPlageconsult;
$where = array();
$where["date"] = "BETWEEN '$deb' AND '$fin'";
$where["chir_id"] = "$in";
$order = array();
$order[] = "date";
$order[] = "chir_id";
$order[] = "debut";
$listPlage = $listPlage->loadList($where, $order);

// Pour chaque plage on selectionne les consultations
foreach($listPlage as $key => $value) {
  $listPlage[$key]->loadRefs();
  foreach($listPlage[$key]->_ref_consultations as $key2 => $value2) {
  	if($listPlage[$key]->_ref_consultations[$key2]->annule)
  	  unset($listPlage[$key]->_ref_consultations[$key2]);
  	else
      $listPlage[$key]->_ref_consultations[$key2]->loadRefs();
  }
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP(1);

$smarty->assign('deb', $deb);
$smarty->assign('fin', $fin);
$smarty->assign('listPlage', $listPlage);

$smarty->display('print_plages.tpl');

?>