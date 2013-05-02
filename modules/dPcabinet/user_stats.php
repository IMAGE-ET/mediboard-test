<?php

/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

CCanDo::checkAdmin();

$type   = CValue::get("type", "RDV");
$date   = CValue::get("date");
$period = CValue::get("period", "month");

$stats = new CMediusersStats($date, $period, "date");

$consult = new CConsultation();
$group = CGroups::loadCurrent();
$ds = $consult->_spec->ds;

$query_complement = "1";
if ($type == "consult") {
  $query_complement = "consultation.chrono > 32
     OR consultation.traitement       IS NOT NULL
     OR consultation.histoire_maladie IS NOT NULL
     OR consultation.conclusion       IS NOT NULL
     OR consultation.examen           IS NOT NULL
     OR consultation.facture_id       IS NOT NULL
  ";
}

if ($type == "fse") {
  $query_complement = "1";
}

$query = "SELECT COUNT(*) total, user_id, $stats->sql_date AS refdate
  FROM `consultation`
  LEFT JOIN plageconsult AS plage ON plage.plageconsult_id = consultation.plageconsult_id
  LEFT JOIN users_mediboard AS user ON user.user_id = plage.chir_id
  LEFT JOIN functions_mediboard AS function ON function.function_id = user.function_id
  WHERE $stats->sql_date BETWEEN '$stats->min_date' AND '$stats->max_date'
  AND function.group_id = '$group->_id'
  AND consultation.annule != '1'
  AND consultation.patient_id IS NOT NULL
  AND ($query_complement)
  GROUP BY user_id, refdate
  ORDER BY refdate DESC
";

foreach ($result = $ds->loadList($query) as $_row) {
  $stats->addTotal($_row["user_id"], $_row["refdate"], $_row["total"]);
}

$stats->display("CMediusersStats-CConsultation-$type");
