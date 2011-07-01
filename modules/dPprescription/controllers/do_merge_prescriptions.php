<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescriptions_ids = array_flip(explode("-", CValue::post("prescriptions_ids")));

$prescription_id = array_keys($prescriptions_ids);
$prescription_id = reset($prescription_id);

$lines = CValue::post("lines");

foreach($lines as $guid=>$keep_line) {
  list($object_class, $id) = explode("-", $guid);
  $object = new $object_class;
  $_POST[$object->_spec->key] = $id;
  $line = new CDoObjectAddEdit($object_class);
  
  // Si on ne garde pas la ligne, on la marque comme arrêtée.
  if (!$keep_line) {
    $line->date_arret = mbDate();
    $line->time_arret = mbTime();
  }
  
  $line->_obj->prescription_id = $prescription_id;
  $line->doBind();
  $line->doStore();
}

// Fusion des prescriptions

$_POST["prescription_id"] = $prescription_id;
$prescription = new CDoObjectAddEdit("CPrescription");

$prescription->doBind();


/*
// Le mode alternatif dans la fonction merge ne permet pas la suppression
// de toutes les prescriptions en une fois

$prescriptions = array();

foreach($prescriptions_ids as $_prescription_id) {
  $prescription = new CPrescription;
  $prescription->load($_prescription_id);
  $prescriptions[] = $prescription;
}

if ($msg = $prescription->_obj->merge($prescriptions)) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}
else {
  CAppUI::setMsg(CAppUI::tr("CPrescription-msg-merge"), UI_MSG_OK);
}*/

foreach ($prescriptions_ids as $_prescription_id=>$elt) {
  $_prescription = new CPrescription;
  $_prescription->load($_prescription_id);
  if ($msg = $prescription->_obj->merge(array(0 => $_prescription))) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
  else {
    CAppUI::setMsg(CAppUI::tr("CPrescription-msg-merge"), UI_MSG_OK);
  }
}

echo CAppUI::getMsg();
CApp::rip();

?>