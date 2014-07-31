<?php 

/**
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

global $date, $chir_id, $print;
$print   = 1;


$function_id  = CValue::get("function_id");
$date         = CValue::get("date");
$start        = CMbDT::date("this monday", $date);
if ($start > $date) {
  $start = CMbDT::date("last monday", $date);
}
$end          = CMbDT::date("next sunday", $start);

$muser = new CMediusers();
$musers = $muser->loadProfessionnelDeSanteByPref(PERM_READ, $function_id);

$function = new CFunctions();
$function->load($function_id);

echo "<h1>".$function->_view."</h1>";

$pconsult = new CPlageconsult();
$ds = $pconsult->getDS();
$where = array();
$where[] = "chir_id ".$ds->prepareIn(array_keys($musers))." OR remplacant_id ".$ds->prepareIn(array_keys($musers));
$where["date"] = " BETWEEN '$start' AND '$end' ";

/** @var CPlageconsult[] $pconsults */
$pconsults = $pconsult->loadList($where, "date", null);

$pconsults_by_date_and_prat = array();

if (!count($pconsults)) {
  echo "<div class='small-info'>Les praticiens de ce cabinet n'ont pas de plages de consultations sur cette période</div>";
  CApp::rip();
}

foreach ($pconsults as $_pc) {
  $chir_id = CValue::get("chir_id", $_pc->chir_id);
  $_pc->loadRefChir();
  $_pc->loadRefRemplacant();
  echo "<h2>";
  echo $_pc->_ref_chir->_view;
  if ($_pc->remplacant_id) {
    echo "remplacé par : ".$_pc->_ref_remplacant->_view;
  }
  echo "</h2>";
  echo CApp::fetch("dPcabinet", "inc_plage_selector_weekly");
}