<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

// Récupération du compte-rendu
$compte_rendu_id = dPgetParam($_GET, "compte_rendu_id", 0);

$compteRendu = new CCompteRendu;
$compteRendu->load($compte_rendu_id);

$cr = $compteRendu->source;

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("cr", $cr);

$smarty->display("print_cr.tpl");

?>