<?php

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 2321 $
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$prestation_id = mbGetValueFromGetOrSession("prestation_id");

// Chargement de la liste des établissements
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// Chargement de la prestation
$prestation = new CPrestation();
$prestation->load($prestation_id);

// Récupération des prestations
$order = "group_id, nom";
$prestations = new CPrestation;
$prestations = $prestations->loadList(null, $order);
foreach($prestations as $keyPrestation=>$valPrestation){
  $prestations[$keyPrestation]->loadRefGroup();
} 


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("prestation"     , $prestation    );
$smarty->assign("prestations"    , $prestations   );
$smarty->assign("etablissements" , $etablissements);

$smarty->display("vw_idx_prestations.tpl");

?>