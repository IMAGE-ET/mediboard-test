<?php /* $Id: edit_sorties.php 783 2006-09-14 12:44:01Z rhum1 $*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 783 $
* @author Romain OLLIVIER
*/

global $can;
$can->needsRead();

$sejour_id = mbGetValueFromGetOrSession("sejour_id");
$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadNumDossier();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour" , $sejour);
$smarty->assign("idImeds", CImeds::getIdentifiants());
$smarty->assign("url"    , CImeds::getDossierUrl());

$smarty->display("inc_sejour_results.tpl");

?>