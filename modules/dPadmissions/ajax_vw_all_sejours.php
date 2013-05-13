<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPadmissions
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */
CCanDo::checkRead();

global $m;

// On sauvegarde le module pour que les mises en session des paramètes se fassent
// dans le module depuis lequel on accède à la ressource
$save_m = $m;

$ds = CSQLDataSource::get("std");

// Initialisation de variables
$current_m     = CValue::get("current_m");

$m = $current_m;

$date          = CValue::getOrSession("date", CMbDT::date());

if (phpversion() >= "5.3") {
  $month_min     = CMbDT::date("first day of +0 month", $date);
  $lastmonth     = CMbDT::date("last day of -1 month" , $date);
  $nextmonth     = CMbDT::date("first day of +1 month", $date);
}
else {
  $month_min     = CMbDT::transform("+ 0 month", $date, "%Y-%m-01");
  $lastmonth     = CMbDT::date("-1 month", $date);
  $nextmonth     = CMbDT::date("+1 month", $date);
  if (CMbDT::transform(null, $date, "%m-%d") == "08-31") {
    $nextmonth = CMbDT::transform("+0 month", $nextmonth, "%Y-09-%d");
  }
  else {
    $nextmonth     = CMbDT::transform("+0 month", $nextmonth, "%Y-%m-01");
  }
}

$recuse        = CValue::getOrSession("recuse", "-1");
$envoi_mail    = CValue::getOrSession("envoi_mail", "0");
$service_id    = CValue::getOrSession("service_id");
$prat_id       = CValue::getOrSession("prat_id");
$bank_holidays = CGroups::loadCurrent()->getHolidays($date);

$hier   = CMbDT::date("- 1 day", $date);
$demain = CMbDT::date("+ 1 day", $date);

// Initialisation du tableau de jours
$days = array();
for ($day = $month_min; $day <= $nextmonth; $day = CMbDT::date("+1 DAY", $day)) {
  $days[$day] = array(
    "num1" => "0",
    "num2" => "0",
    "num3" => "0",
  );
}

if ($current_m == "reservation") {
  for ($day = $month_min; $day <= $nextmonth; $day = CMbDT::date("+1 DAY", $day)) {
    $days[$day]["num4"] = "0";
  }
}

// filtre sur les types d'admission
$filterType = "";
if ($current_m == "ssr") {
  $filterType = "AND (`sejour`.`type` = 'ssr')";
}

// filtre sur les services
if ($service_id) {
  $leftjoinService = "LEFT JOIN affectation
                        ON affectation.sejour_id = sejour.sejour_id AND affectation.sortie = sejour.sortie_prevue
                      LEFT JOIN lit
                        ON affectation.lit_id = lit.lit_id
                      LEFT JOIN chambre
                        ON lit.chambre_id = chambre.chambre_id
                      LEFT JOIN service
                        ON chambre.service_id = service.service_id";
  $filterService = "AND service.service_id = '$service_id'";
}
else {
  $leftjoinService = $filterService = "";
}

// filtre sur le praticien
if ($prat_id) {
  $filterPrat = "AND sejour.praticien_id = '$prat_id'";
}
else {
  $filterPrat = "";
}

$group = CGroups::loadCurrent();

// Liste des séjours en attente par jour
$query = "SELECT DATE_FORMAT(`sejour`.`entree`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
  FROM `sejour`
  $leftjoinService
  WHERE `sejour`.`entree` BETWEEN '$month_min' AND '$nextmonth'
    AND `sejour`.`group_id` = '$group->_id'
    AND `sejour`.`recuse` = '-1'
    AND `sejour`.`annule` = '0'
    $filterType
    $filterService
    $filterPrat
  GROUP BY `date`
  ORDER BY `date`";
foreach ($ds->loadHashList($query) as $day => $num1) {
  $days[$day]["num1"] = $num1;
}

// Liste des séjours validés par jour
$query = "SELECT DATE_FORMAT(`sejour`.`entree`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
  FROM `sejour`
  $leftjoinService
  WHERE `sejour`.`entree_prevue` BETWEEN '$month_min' AND '$nextmonth'
    AND `sejour`.`group_id` = '$group->_id'
    AND `sejour`.`recuse` = '0'
    AND `sejour`.`annule` = '0'
    $filterType
    $filterService
    $filterPrat
  GROUP BY `date`
  ORDER BY `date`";
foreach ($ds->loadHashList($query) as $day => $num2) {
  $days[$day]["num2"] = $num2;
}

// Liste des séjours récusés par jour
$query = "SELECT DATE_FORMAT(`sejour`.`entree`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
    FROM `sejour`
  $leftjoinService
  WHERE `sejour`.`entree` BETWEEN '$month_min' AND '$nextmonth'
    AND `sejour`.`group_id` = '$group->_id'
    AND `sejour`.`recuse` = '1'
    $filterType
    $filterService
    $filterPrat
  GROUP BY `date`
  ORDER BY `date`";
foreach ($ds->loadHashList($query) as $day => $num3) {
  $days[$day]["num3"] = $num3;
}


if ($current_m == "reservation") {
  // Une nouvelle colonne de la forme : Mails répondus / Mails envoyés
  
  // Mails envoyés
  $query = "SELECT DATE_FORMAT(`sejour`.`entree`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
    FROM `sejour`
    $leftjoinService
    LEFT JOIN `operations` ON `operations`.`sejour_id` = `sejour`.`sejour_id`
    WHERE `sejour`.`entree` BETWEEN '$month_min' AND '$nextmonth'
      AND `operations`.`operation_id` IS NOT NULL
      AND `operations`.`envoi_mail` IS NOT NULL
      AND `sejour`.`group_id` = '$group->_id'
      AND `sejour`.`recuse` != '1'
      $filterType
      $filterService
      $filterPrat
    GROUP BY `date`
    ORDER BY `date`";
  foreach ($ds->loadHashList($query) as $day => $num4) {
    $days[$day]["num4"] = "0/$num4";
  }
  
  // Mails répondus
  // Sur la base d'un user log du praticien (qui a donc modifié la DHE)
  // On ajout DISTINCT sur le sejour_id, car il peut y avoir plusieurs entrées dans la table
  // user_log qui correspondent
  $query = "SELECT DATE_FORMAT(`sejour`.`entree`, '%Y-%m-%d') AS `date`, COUNT(DISTINCT `sejour`.`sejour_id`) AS `num`
    FROM `sejour`
    $leftjoinService
    LEFT JOIN `operations` ON `operations`.`sejour_id` = `sejour`.`sejour_id`
    LEFT JOIN `user_log` ON `user_log`.`user_id` = `operations`.`chir_id`
      AND `user_log`.`object_class` = 'COperation' AND `user_log`.`object_id` = `operations`.`operation_id`
    WHERE `sejour`.`entree` BETWEEN '$month_min' AND '$nextmonth'
      AND `operations`.`operation_id` IS NOT NULL
      AND `operations`.`envoi_mail` IS NOT NULL
      AND `user_log`.`user_log_id` IS NOT NULL
      AND `sejour`.`group_id` = '$group->_id'
      AND `sejour`.`recuse` != '1'
      $filterType
      $filterService
      $filterPrat
    GROUP BY `date`
    ORDER BY `date`";
  
  foreach ($ds->loadHashList($query) as $day => $num4) {
    $days[$day]["num4"] = preg_replace("/0\//", "$num4/", $days[$day]["num4"]);
  }
}

$m = $save_m;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("current_m"    , $current_m);
$smarty->assign("hier"         , $hier);
$smarty->assign("demain"       , $demain);

$smarty->assign("recuse"       , $recuse);
$smarty->assign("envoi_mail"   , $envoi_mail);

$smarty->assign("bank_holidays", $bank_holidays);
$smarty->assign('date'         , $date);
$smarty->assign('lastmonth'    , $lastmonth);
$smarty->assign('nextmonth'    , $nextmonth);
$smarty->assign('days'         , $days);

$smarty->display('inc_vw_all_sejours.tpl');
