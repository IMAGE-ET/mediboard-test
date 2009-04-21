<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Thomas Depoix
*/

global $can;
$can->needsAdmin();

// Vérification des droits sur les praticiens
$mediuser = new CMediusers();
$praticiens = $mediuser->loadPraticiens(PERM_EDIT);

// Filtre
$filter = new CPlageconsult();

if ($filter->chir_id =  mbGetValueFromGetOrSession("chir_id")) {
  $where["chir_id"] = "= '$filter->chir_id'";
}

if ($filter->_date_min = mbGetValueFromGetOrSession("_date_min")) {
  $where[] = "date >= '$filter->_date_min'";
}

if ($filter->_date_max = mbGetValueFromGetOrSession("_date_max")) {
  $where[] = "date <= '$filter->_date_max'";
}

// Chargement des plages
$plages = array();
if ($filter->chir_id) {
  $plages = $filter->loadList($where, "date");
  foreach($plages as $_plage) {
    $_plage->loadFillRate();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("praticiens", $praticiens);
$smarty->assign("plages"    , $plages    );
$smarty->assign("filter"    , $filter    );

$smarty->display("transfert_plageconsult.tpl");
?>