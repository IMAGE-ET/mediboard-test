<?php /* $Id: edit_sorties.php 783 2006-09-14 12:44:01Z rhum1 $*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 783 $
* @author Romain OLLIVIER
*/

global $AppUI, $can;
$can->needsRead();

// Chargement des identifiants externes de l'établissement pour Imeds
$etablissement = CGroups::loadCurrent();

$idImeds = array();
$id400 = new CIdSante400;

$id400->loadLatestFor($etablissement, "Imeds cidc");
$idImeds["cidc"] = $id400->id400;
$id400 = new CIdSante400;
$id400->loadLatestFor($etablissement, "Imeds cdiv");
$idImeds["cdiv"] = $id400->id400;
$id400 = new CIdSante400;
$id400->loadLatestFor($etablissement, "Imeds csdv");
$idImeds["csdv"] = $id400->id400;
$id400 = new CIdSante400;

// Chargement de l'utilisateur courant
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);

// Chargement des id externes du user courant
$id400 = new CIdSante400();
$id400->loadLatestFor($mediuser, "Imeds_login");
$idImeds["login"] = $id400->id400;
$id400 = new CIdSante400();
$id400->loadLatestFor($mediuser, "Imeds_password");
$idImeds["password"] = md5($id400->id400);

$prescription_id = mbGetValueFromGetOrSession("prescription_id");
$prescription = new CPrescriptionLabo();
$prescription->load($prescription_id);
$idPresc = $prescription->getIdExterne();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("prescription" , $prescription);
$smarty->assign("idImeds", $idImeds);
$smarty->assign("numPresc", $idPresc->id400);
$smarty->assign("url"    , CAppUI::conf("dPImeds url"));

$smarty->display("inc_prescription_results.tpl");

?>