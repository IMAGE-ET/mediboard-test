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
$today = mbDate();

// Nombre de patients
$where = array("entree" => ">= '$today 00:00:00'");
$sejour = new Csejour();
$nb_sejours = $sejour->countList($where);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("minutes"    , $minutes);
$smarty->assign("hours"      , $hours);
$smarty->assign("today"      , $today);
$smarty->assign("nb_sejours" , $nb_sejours);
$smarty->assign("intervals"  , $intervals);
$smarty->assign("patient_ids", $patient_ids);

$smarty->display("configure.tpl");
?>