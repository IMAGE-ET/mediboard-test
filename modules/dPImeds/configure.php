<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $can;
$can->needsAdmin();

$today = mbDateTime();

// Chargement de l'etablissement courant
$etab = CGroups::loadCurrent();

// Chargement des id400 de l'etablissement courant
$idCSDV = new CIdSante400();
$idCSDV->loadLatestFor($etab, "Imeds csdv");

$idCDIV = new CIdSante400();
$idCDIV->loadLatestFor($etab,"Imeds cdiv");

$idCIDC = new CIdSante400();
$idCIDC->loadLatestFor($etab, "Imeds cidc");


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("idCSDV", $idCSDV);
$smarty->assign("idCDIV", $idCDIV);
$smarty->assign("idCIDC", $idCIDC);
$smarty->assign("etab",   $etab);
$smarty->assign("today",  $today);
$smarty->assign("soap_path", CImeds::$soap_path);
$smarty->assign("soap_url" , CImeds::getSoapUrl());

$smarty->display("configure.tpl");

?>