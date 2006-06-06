<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPpatients', 'patients') );
require_once( $AppUI->getModuleClass('dPcabinet', 'plageconsult') );
require_once( $AppUI->getModuleClass('dPcabinet', 'consultation') );

$canEdit = !getDenyEdit($m);
if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$chir = new CMediusers;
$chir->load($_POST["prat_id"]);

$day_now = strftime("%Y-%m-%d");
$hour_now = strftime("%H:%M:00");
$debut = strftime("%H:00:00");

$plage = new CPlageConsult();
$where = array();
$where["chir_id"] = "= '$chir->user_id'";
$where["date"] = "= '$day_now'";
$where["debut"] = "<= '$hour_now'";
$where["fin"] = "> '$hour_now'";
$plage->loadObject($where);
if(!$plage->plageconsult_id) {
  $plage->chir_id = $chir->user_id;
  $plage->date = $day_now;
  $plage->freq = "00:15:00";
  $plage->debut = $debut;
  $plage->fin = mbTime("+1 HOUR", $debut);
  $plage->libelle = "automatique";
  $plage->store();
}

$consult = new CConsultation;
$consult->plageconsult_id = $plage->plageconsult_id;
$consult->patient_id = $_POST["patient_id"];
$consult->heure = $hour_now;
$consult->arrivee = $hour_now;
$consult->duree = 1;
$consult->chrono = CC_PATIENT_ARRIVE;
$consult->motif = "Consultation immédiate";
$consult->store();

$AppUI->redirect("m=dPcabinet&tab=edit_consultation&selConsult=$consult->consultation_id&chirSel=$chir->user_id");

?>