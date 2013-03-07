<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Sébastien Fillonneau
*/

CCanDo::checkAdmin();

$hours = range(0, 23);
$minutes = range(0, 59);
$intervals = array("5","10","15","20","30");
$patient_ids = array("0", "1", "2");
$today = CMbDT::date();

// Nombre de patients
$where = array("entree" => ">= '$today 00:00:00'");
$sejour = new CSejour();
$nb_sejours = $sejour->countList($where);

$cpi = new CChargePriceIndicator;
$list_cpi = $cpi->loadGroupList();

$mode_entree = new CModeEntreeSejour();
$list_modes_entree = $mode_entree->loadGroupList();

$mode_sortie = new CModeSortieSejour();
$list_modes_sortie = $mode_sortie->loadGroupList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("minutes"    , $minutes);
$smarty->assign("hours"      , $hours);
$smarty->assign("today"      , $today);
$smarty->assign("nb_sejours" , $nb_sejours);
$smarty->assign("intervals"  , $intervals);
$smarty->assign("patient_ids", $patient_ids);
$smarty->assign("list_cpi", $list_cpi);
$smarty->assign("list_modes_entree", $list_modes_entree);
$smarty->assign("list_modes_sortie", $list_modes_sortie);

$smarty->display("configure.tpl");
