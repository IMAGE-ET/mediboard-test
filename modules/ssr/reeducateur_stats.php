<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$type   = CValue::get("type", "CEvenementSSR");
$date   = CValue::get("date");
$period = CValue::get("period", "month");

$stats = new CMediusersStats($date, $period, "DATE(debut)");

$consult = new CConsultation();
$ds = $consult->_spec->ds;

$group = CGroups::loadCurrent();

switch ($type) {
  case "CEvenementSSR":
    $from = "`evenement_ssr`";
    break;

  case "CActeCdARR":
    $from = "`acte_cdarr`
      LEFT JOIN evenement_ssr ON acte_cdarr.evenement_ssr_id = evenement_ssr.evenement_ssr_id
    ";
    break;

  case "CActeCsARR":
    $from = "`acte_csarr`
      LEFT JOIN evenement_ssr ON acte_csarr.evenement_ssr_id = evenement_ssr.evenement_ssr_id
    ";
    break;

  default:
    trigger_error(E_USER_WARNING, "Type '$type' unknown");
    return;
}

$query = "SELECT COUNT(*) total, therapeute_id AS user_id, $stats->sql_date AS refdate
  FROM $from
  LEFT JOIN sejour ON sejour.sejour_id = evenement_ssr.sejour_id
  LEFT JOIN users_mediboard AS user ON user.user_id = evenement_ssr.therapeute_id
  LEFT JOIN functions_mediboard AS function ON function.function_id = user.function_id
  WHERE $stats->sql_date BETWEEN '$stats->min_date' AND '$stats->max_date'
  AND function.group_id = '$group->_id'
  AND evenement_ssr.annule != '1'
  GROUP BY therapeute_id, refdate
  ORDER BY refdate DESC
";

foreach ($result = $ds->loadList($query) as $_row) {
  $stats->addTotal($_row["user_id"], $_row["refdate"], $_row["total"]);
}

$stats->display("CMediusersStats-SSR-$type");
