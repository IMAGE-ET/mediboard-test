<?php

/**
 * dPhospi
 *  
 * @category dPhospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$date_min   = CValue::getOrSession("date_min", mbDate("-1 month"));
$date_max   = CValue::getOrSession("date_max", mbDate());
$service_id = CValue::getOrSession("service_id");

$service = new CService;
$services = $service->loadListWithPerms(PERM_READ);

// Template avec échec
$smarty = new CSmartyDP;
$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);
$smarty->assign("type", "occupation");
$smarty->assign("service_id", $service_id);
$smarty->assign("services", $services);

if (!$service_id) {
  $smarty->display("inc_form_stats.tpl");
  CAppUI::stepMessage(UI_MSG_ALERT  , "warning-hospi-stats-choose_service");
  return;
}

$ds        = CSQLDataSource::get("std");
$dates     = array();
$date_temp = $date_min;
$series    = array();

while ($date_temp <= $date_max) {
  $dates[] = array(count($dates), mbDateToLocale($date_temp));
  $date_temp = mbDate("+1 day", $date_temp);
}

// Table temporaraire de dates pour les jointures
$tab_name = CSQLDataSource::tempTableDates($date_min, $date_max);

// Nombre de lits totaux sur le service
$lit = new CLit();

$where = array();
$ljoin = array();

$ljoin["chambre"] = "chambre.chambre_id = lit.chambre_id";
$where["service_id"] = " = '$service_id'";
$where["annule"] = " = '0'";

$nb_lits = $lit->countList($where, null, $ljoin);
if (!$nb_lits) {
  $smarty->display("inc_form_stats.tpl");
  CAppUI::stepMessage(UI_MSG_WARNING  , "warning-hospi-stats-no_beds");
  return;
}

// Lits ouverts (non bloqués - non compris les blocages des urgence)
$serie_lits_ouverts = array(
  "data" => array(),
  "label" => utf8_encode("Ouvert / Total"),
  "markers" => array("show" => true)
);
// Sauvegarde des lits ouverts par date
$lits_ouverts_par_date = array();

// Version avec la table temporaire
// Non fonctionnelle
/*$query = "SELECT d.date, count(l.lit_id) as lits_ouverts
  FROM $tab_name d
  INNER JOIN lit l
  JOIN affectation a ON a.lit_id = l.lit_id AND
    DATE_FORMAT(a.entree, '%Y-%m-%d') <= d.date AND DATE_FORMAT(a.sortie, '%Y-%m-%d') >= d.date
    AND a.sejour_id != 0
  LEFT JOIN chambre c ON c.chambre_id = l.chambre_id
  WHERE  c.service_id = '$service_id'
  GROUP BY d.date
  ORDER BY d.date";

$lits_ouverts = $ds->loadList($query);

foreach ($lits_ouverts as $_lits_by_day) {
  $serie_lits_ouverts['data'][] =
    array(count($serie_lits_ouverts['data']) + 0.1,
          $_lits_by_day["lits_ouverts"],
          $_lits_by_day["lits_ouverts"] / $nb_lits);
  
  $lits_ouverts_par_date[$_lits_by_day["date"]] = $_lits_by_day["lits_ouverts"];
}*/

foreach ($dates as $key=>$_date) {
  $query = "SELECT count(DISTINCT l.lit_id) as lits_ouverts
    FROM lit l
    JOIN affectation a ON a.lit_id = l.lit_id AND
    DATE_FORMAT(a.entree, '%Y-%m-%d') <= '".mbDateFromLocale($_date[1])."' AND DATE_FORMAT(a.sortie, '%Y-%m-%d') >= '".mbDateFromLocale($_date[1])."'
    AND a.sejour_id != 0
    LEFT JOIN chambre c ON c.chambre_id = l.chambre_id
    WHERE  c.service_id = '$service_id'";
  $lits_ouverts = $ds->loadResult($query);
  
  $serie_lits_ouverts['data'][] =
    array(count($serie_lits_ouverts['data']) + 0.1,
          $lits_ouverts,
          $lits_ouverts / $nb_lits);
  $lits_ouverts_par_date[mbDateFromLocale($_date[1])] = $lits_ouverts;
}

// Prévu (séjours)
// WHERE s.service_id = '$service_id' => beh, le service_id est pas notNull (en config)
$query = "SELECT d.date, count(sejour_id) nb_prevu
  FROM $tab_name d
  LEFT JOIN sejour s ON
    s.entree_prevue <= DATE_FORMAT(d.date, '%Y-%m-%d 00:00:00') AND s.entree_prevue >= DATE_FORMAT(d.date, '%Y-%m-%d 00:00:00')
  WHERE s.service_id = '$service_id'
  GROUP BY d.date
  ORDER BY d.date";

$prevu = $ds->loadList($query);

$serie = array(
  "data" => array(),
  "label" => utf8_encode("Prévu"),
  "markers" => array("show" => true)
);

foreach ($prevu as $_prevu_by_day) {
  $serie["data"][] =
    array(count($serie["data"])-0.20,
          $_prevu_by_day["nb_prevu"],
          isset($lits_ouverts_par_date[$_prevu_by_day["date"]]) && $lits_ouverts_par_date[$_prevu_by_day["date"]] != 0?
            $_prevu_by_day["nb_prevu"] / $lits_ouverts_par_date[$_prevu_by_day["date"]] : 0);
}

$series[] = $serie;

// Réel (affectations)
$query = "SELECT d.date, count(affectation_id) as nb_reel
  FROM $tab_name d
  LEFT JOIN affectation a ON
    a.entree <= DATE_FORMAT(d.date, '%Y-%m-%d 00:00:00') AND a.entree >= DATE_FORMAT(d.date, '%Y-%m-%d 00:00:00')
  WHERE a.service_id = '$service_id'
  GROUP BY d.date
  ORDER BY d.date";

$reel = $ds->loadList($query);

$serie = array(
  "data" => array(),
  "label" => utf8_encode("Réel"),
  "markers" => array("show" => true)
);

foreach ($reel as $_reel_by_day) {
  $serie["data"][] =
    array(count($serie['data']) - 0.05,
          $_reel_by_day["nb_reel"],
          isset($lits_ouverts_par_date[$_reel_by_day["date"]]) && $lits_ouverts_par_date[$_reel_by_day["date"]] != 0 ?
            $_reel_by_day["nb_reel"] / $lits_ouverts_par_date[$_reel_by_day["date"]] : 0);
}

$series[] = $serie;

// Ajout en troisième série les lits ouverts
$series[] = $serie_lits_ouverts;

// Entrées dans la journée (nb de placements sur tous les lits sur chaque journée)
// Ne pas compter les blocages
$query = "SELECT d.date, count(affectation_id) as entrees
  FROM $tab_name d
  LEFT JOIN affectation a ON
    DATE_FORMAT(a.entree, '%Y-%m-%d') <= d.date AND DATE_FORMAT(a.sortie, '%Y-%m-%d') >= d.date
    AND a.sejour_id != 0 AND a.service_id = '$service_id'
  GROUP BY d.date
  ORDER BY d.date";

$entrees_journee = $ds->loadList($query);

$serie = array(
  "data" => array(),
  "label" => utf8_encode("Entrées"),
  "markers" => array("show" => true)
);

foreach ($entrees_journee as $_entrees_by_day) {
  $serie["data"][] =
    array(count($serie['data']) + 0.25,
          $_entrees_by_day["entrees"],
          isset($lits_ouverts_par_date[$_entrees_by_day["date"]]) && $lits_ouverts_par_date[$_entrees_by_day["date"]] != 0 ?
            $_entrees_by_day["entrees"] / $lits_ouverts_par_date[$_entrees_by_day["date"]] : 0);
}

$series[] = $serie;

$options = CFlotrGraph::merge("bars", array(
  "title"    => utf8_encode("Taux d'occupation"),
  "xaxis"    => array("ticks" => $dates),
  "grid"     => array("verticalLines" => true),
  "bars"     => array("barWidth" => 0.15, "stacked" => true)
));

$graph = array("series" => $series, "options" => $options);

$smarty = new CSmartyDP;

$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);
$smarty->assign("services", $services);
$smarty->assign("graph"   , $graph);
$smarty->assign("service_id", $service_id);

$smarty->display("inc_vw_stats_occupation.tpl");

?>