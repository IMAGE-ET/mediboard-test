<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

global $date, $chir_id, $print;

$date    = CValue::get("date");
$chir_id = CValue::get("chir_id");
$print   = 1;

if (!$chir_id) {
  echo "<div class='small-info'>".CAppUI::tr("CConsultation-no_chir_planning")."</div>";
  CApp::rip();
}
echo CApp::fetch("dPcabinet", "inc_plage_selector_weekly");
