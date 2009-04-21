<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$fiche_ATC_id = mbGetValueFromGetOrSession("fiche_ATC_id");

// Chargement de la fiche selectionnee
$fiche_ATC = new CFicheATC();
if($fiche_ATC_id){
  $fiche_ATC->load($fiche_ATC_id);
  $fiche_ATC->getLibelleATC();
	$templateManager = new CTemplateManager();
	$templateManager->editor = "fckeditor";
	$templateManager->simplifyMode = true;
	$templateManager->printMode = true;
	$templateManager->initHTMLArea();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("fiche_ATC", $fiche_ATC);
$smarty->display("vw_fiche_ATC.tpl");

?>
