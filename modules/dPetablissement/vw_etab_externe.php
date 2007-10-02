<?php

/**
* @package Mediboard
* @subpackage dPetablissement
* @version $Revision: 
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$can->needsRead();

// Recuperation de l'id de l'etablissement externe
$etab_id = mbGetValueFromGetOrSession("etab_id");

// Récupération des etablissements externes
$etabExterne = new CEtabExterne();
$listEtabExternes = $etabExterne->loadList();

if($etab_id){
  $etabExterne->load($etab_id);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listEtabExternes", $listEtabExternes );
$smarty->assign("etabExterne"     , $etabExterne      );

$smarty->display("vw_etab_externe.tpl");

?>