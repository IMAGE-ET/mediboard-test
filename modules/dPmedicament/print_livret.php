<?php

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

// Chargement de l'etablissement courant
$etablissement = CGroups::loadCurrent();

// Chargement des produits du livret therapeutique
$etablissement->loadRefLivretTherapeutique('%', 2000, true);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date", mbDate());
$smarty->assign("produits_livret", $etablissement->_ref_produits_livret);

$smarty->display("print_livret.tpl");

?>