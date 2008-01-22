<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m;
$ds = CSQLDataSource::get("ccamV2");

$quantite = mbGetValueFromGetOrSession("quantite");
$coefficient = mbGetValueFromGetOrSession("coefficient");

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

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("tarif"      , $tarif);
$smarty->assign("acte_ngap"  , new CActeNGAP());
$smarty->display("inc_vw_tarif_ngap.tpl");


?>