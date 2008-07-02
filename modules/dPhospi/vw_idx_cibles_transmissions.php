<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$categorie_cible_transmission_id = mbGetValueFromGetOrSession("categorie_cible_transmission_id", 0);
$cible_transmission_id           = mbGetValueFromGetOrSession("cible_transmission_id", 0);
if(mbGetValueFromGet("categorie_cible_transmission_id", 0)) {
  $cible_transmission_id = 0;
  mbSetValueToSession("cible_transmission_id");
}

// Récupération de la catégorie de cible selectionnée
$categorie = new CCategorieCibleTransmission();
$categorie->load($categorie_cible_transmission_id);
$categorie->loadBackRefs("cibles");

// Récupération de la cibl selectionnée
$cible = new CCibleTransmission();
$cible->load($cible_transmission_id);

// Récupération des services
$order = "libelle";
$where = array();
$categories = $categorie->loadList($where, $order);

foreach($categories as &$categorie) {
  $categorie->loadBackRefs("cibles");
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("categories", $categories);
$smarty->assign("categorie" , $categorie );
$smarty->assign("cible"     , $cible     );

$smarty->display("vw_idx_cibles_transmissions.tpl");

?>