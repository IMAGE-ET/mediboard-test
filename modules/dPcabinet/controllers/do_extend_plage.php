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

CCanDo::checkEdit();

$plage_id     = CValue::get("plage_id");
$repeat       = min(CValue::get("_repeat", 0)+1, 100);
$repeat_mode  = CValue::get("_type_repeat");

//if 1: current week, 2: next week
$nb_extend = $repeat++;

//load plage
$plage_consult = new CPlageconsult();
$plage_consult->load($plage_id);
$plage_consult->_type_repeat = $repeat_mode;

//behaviour
$skipped = 0;
$created = 0;
$failed = 0;

//do the repeat work (2 = startng next week)
while (2 <= $nb_extend) {
  //plage doesn't exist
  if (!$plage_consult->_id) {
    if ($msg = $plage_consult->store()) {
      $failed++;
    }
    else {
      $created++;
    }
  }
  //exist = skipped
  else {
    $skipped++;
  }
  $nb_extend -= $plage_consult->becomeNext();
}

CAppUI::setMsg("Cplageconsult-msg-extend_finished_nb%d_week", UI_MSG_OK, $repeat);
if ($created) {
  CAppUI::setMsg('Cplageconsult-msg-creation_created_nb%d', UI_MSG_OK, $created);
}

if ($skipped) {
  CAppUI::setMsg('Cplageconsult-msg-creation_skipped_nb%d', UI_MSG_ALERT, $skipped);
}

if ($failed) {
  CAppUI::setMsg('Cplageconsult-msg-creation_failed_nb%d', UI_MSG_ERROR, $failed);
}

echo CAppUI::getMsg();