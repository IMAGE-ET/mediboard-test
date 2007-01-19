<?php /* $Id: edit_compta.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision: 23 $
 * @author Romain Ollivier
 */

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}
$fiche_paie_id = mbGetValueFromGetOrSession("fiche_paie_id", null);

$fichePaie = new CFichePaie();
$fichePaie->load($fiche_paie_id);
if(!$fichePaie->fiche_paie_id) {
  $AppUI->setMsg("Vous n'avez pas choisi de fiche de paie", MSG_ERROR);
  $AppUI->redirect( "m=dPgestionCab&tab=edit_paie" );
}
$fichePaie->loadRefsFwd();
$fichePaie->_ref_params_paie->loadRefsFwd();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("fichePaie" , $fichePaie);

$smarty->display("print_fiche.tpl");
?>