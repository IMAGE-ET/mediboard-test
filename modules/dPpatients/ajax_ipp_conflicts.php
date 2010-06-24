<?php 

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$id400 = new CIdSante400();
$id400->object_class = "CPatient";
$id400->tag = CAppUI::conf("dPpatients CPatient tag_conflict_ipp").CAppUI::conf("dPpatients CPatient tag_ipp");
$ipp_conflicts = $id400->loadMatchingList();

$conflicts = array();
foreach ($ipp_conflicts as $_conflict) {
  $patient_conflict = new CPatient();
  $patient_conflict->load($_conflict->object_id);
  $patient_conflict->loadIPP();

  $IPP = new CIdSante400();
  $IPP->object_class = "CPatient";
  $IPP->tag = CAppUI::conf("dPpatients CPatient tag_ipp");
  $IPP->id400 = $_conflict->id400;
  $IPP->loadMatchingObject();

  $patient = new CPatient();
  $patient->load($IPP->object_id);
  
  $patient->loadIPP();
  
  $conflicts[] = array (
    "patient"          => $patient,
    "patient_conflict" => $patient_conflict
  );
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("conflicts", $conflicts);

$smarty->display("inc_ipp_conflicts.tpl");
?>