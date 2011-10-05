<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 10762 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id = CValue::post("prescription_id");
$pratSel_id      = CValue::post("pratSel_id");
$praticien_id    = CValue::post("praticien_id");
$lines           = CValue::post("lines");

$user_id = CMediusers::get()->_id;

foreach($lines as $guid=>$keep_line) {
  list($object_class, $id) = explode("-", $guid);
  $object = new $object_class;
  $_POST[$object->_spec->key] = $id;
  if (!$keep_line) {
    $line = new CDoObjectAddEdit($object_class);
    $line->doBind();
    $line->doDelete();
  }
  else {
    $line = CMbObject::loadFromGuid($guid);
    if ($line->praticien_id == $user_id) {
      if ($line instanceof CPrescriptionLineMix) {
        $line->signature_prat = 1;
      }
      else {
        $line->signee = 1;
      }
    }
    if ($msg = $line->store()) {
      return $msg;
    }
  }
}

echo CAppUI::getMsg();
CApp::rip();

?>