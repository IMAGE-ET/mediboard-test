<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Fabien Ménager
*/


global $AppUI, $can, $m;

$can->needsEdit();

$user_id = mbGetValueFromGetOrSession("user_id");

// On charge le praticien
$user = new CMediusers;
$user->load($user_id);
$user->loadRefs();
$canUser = $user->canDo();

// Vérification des droits sur les praticiens
$listChir = $user->loadPraticiens(PERM_EDIT);
$canUser->needsEdit(array("chirSel"=>0));

// Chargement des aides à la saisie
$addiction = new CAddiction();
$addiction->loadAides($user->user_id);

$antecedent = new CAntecedent();
$antecedent->loadAides($user->user_id);

$traitement = new CTraitement();
$traitement->loadAides($user->user_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("addiction" , $addiction);
$smarty->assign("antecedent", $antecedent);
$smarty->assign("traitement", $traitement);

$smarty->display("vw_ant_easymode.tpl");
?>