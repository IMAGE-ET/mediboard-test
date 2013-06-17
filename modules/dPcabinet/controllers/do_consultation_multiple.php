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


//global
$patient_id   = CValue::post("patient_id");
$rques        = CValue::post("rques");
$motif        = CValue::post("motif");
$chrono       = CValue::post("chrono");
$premiere     = CValue::post("premiere");
$pause        = CValue::post("_pause");


for ($a = 2; $a <= CAppUI::pref("NbConsultMultiple"); $a ++) {
  $_heure     = CValue::post("heure_$a");
  $_plage_id  = CValue::post("plage_id_$a");
  $_date      = CValue::post("date_$a");
  $_chir_id   = CValue::post("chir_id_$a");
  $_rques     = CValue::post("rques_$a");

  if ($_heure && $_plage_id && $_date && $_chir_id) {
    $consult = new CConsultation();
    $consult->heure = $_heure;
    $consult->plageconsult_id = $_plage_id;
    $consult->_pause = $pause;
    $consult->patient_id = $patient_id;
    $consult->motif = $motif;
    $consult->rques = $_rques ? "$rques\n$_rques" : $rques;
    $consult->chrono = $chrono;
    $consult->premiere = $premiere;
    if ($msg = $consult->store()) {
      CAppUI::setMsg(CAppUI::tr("CConsultation")."$a :".$msg, UI_MSG_ERROR);
    }
  }
}