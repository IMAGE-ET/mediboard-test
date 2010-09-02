<?php /* $Id: vw_recherche.php 8520 2010-04-09 14:27:59Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 8520 $
* @author Alexis Granger
*/

// save form info
$patient = new CPatient;
bindHashToObject($_GET + $_SESSION["dPpatients"], $patient);
$patient->loadRefsFwd();

$sejour = new CSejour;
bindHashToObject($_GET + $_SESSION["dPpatients"], $sejour);
$sejour->loadRefsFwd();

$interv = new COperation;
bindHashToObject($_GET + $_SESSION["dPpatients"], $interv);
$interv->loadRefsFwd();

$dm = new CDossierMedical;
bindHashToObject($_GET + $_SESSION["dPpatients"], $dm);
$dm->loadRefsFwd();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("patient", $patient);
$smarty->assign("sejour", $sejour);
$smarty->assign("interv", $interv);
$smarty->assign("dossier_medical", $dm);
$smarty->display("vw_recherche_dossier_clinique.tpl");
