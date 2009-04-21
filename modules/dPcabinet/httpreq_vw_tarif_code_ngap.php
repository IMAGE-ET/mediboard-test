<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Alexis Granger
*/

global $AppUI, $can, $m;
$ds = CSQLDataSource::get("ccamV2");

$quantite = mbGetValueFromGetOrSession("quantite");
$coefficient = mbGetValueFromGetOrSession("coefficient");
$demi = mbGetValueFromGetOrSession("demi", 0);
$complement = mbGetValueFromGetOrSession("complement");

if($quantite == ""){
  $quantite = 1;
}

if($coefficient == ""){
  $coefficient = 1;
}

$code = mbGetValueFromGetOrSession("code");

$sql = "SELECT `tarif` FROM `codes_ngap` WHERE `code` = '$code' ";
$tarif = $ds->loadList($sql, null);
$tarif = reset($tarif);

$tarif = $tarif["tarif"] * $quantite * $coefficient; 
if($demi == 1){
  $tarif = $tarif / 2;
}

if($complement && $complement != "U"){
  if($complement == "F"){
  	$tarif += 19.06;	
  }
  if($complement == "N"){
  	$tarif += 25;	
  }
}


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("tarif"      , $tarif);
$smarty->assign("acte_ngap"  , new CActeNGAP());
$smarty->display("inc_vw_tarif_ngap.tpl");


?>