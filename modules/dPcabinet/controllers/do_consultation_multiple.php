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
$patient_id = CValue::post("patient_id");
$rques      = CValue::post("rques");
$motif      = CValue::post("motif");
$chrono     = CValue::post("chrono");
$premiere   = CValue::post("premiere");
$pause      = CValue::post("_pause");

$keep_motif = CAppUI::conf("dPcabinet CConsultation keep_motif_rdv_multiples", CGroups::loadCurrent());

for ($a = 1; $a <= CAppUI::pref("NbConsultMultiple"); $a ++) {
  $_consult_id     = CValue::post("consult_id_$a");
  $_heure          = CValue::post("heure_$a");
  $_plage_id       = CValue::post("plage_id_$a");
  $_date           = CValue::post("date_$a");
  $_chir_id        = CValue::post("chir_id_$a");
  $_rques          = CValue::post("rques_$a");
  $_cancel         = CValue::post("cancel_$a", 0);
  $_precription_id = CValue::post("element_prescription_id_$a");
  $_category_id    = CValue::post("category_id_$a");

  if ($_heure && $_plage_id && $_chir_id) {
    $consult = new CConsultation();
    if ($_consult_id) {
      $consult->load($_consult_id);
    }
    if (!$pause) {
      $consult->patient_id      = $patient_id;
    }
    else {
      $consult->patient_id = null;
    }

    if ($_category_id) {
      $cat = new CConsultationCategorie();
      $cat->load($_category_id);
      if ($cat->_id) {
        $consult->duree         = $duree = $cat->duree;
        $consult->categorie_id  = $cat->_id;
      }
    }


    $consult->plageconsult_id         = $_plage_id;
    $consult->heure                   = $_heure;
    if ($keep_motif) {
      $consult->motif                 = $motif;
    }
    $consult->rques                   = $_rques ? "$rques\n$_rques" : $rques;
    $consult->chrono                  = $chrono;
    $consult->premiere                = $premiere;
    $consult->annule                  = $_cancel;
    $consult->element_prescription_id = $_precription_id;
    $consult->_hour                   = null;
    $consult->_min                    = null;

    if ($msg = $consult->store()) {
      CAppUI::setMsg(CAppUI::tr("CConsultation")."$a :".$msg, UI_MSG_ERROR);
    }
  }
}

echo CAppUI::getMsg();