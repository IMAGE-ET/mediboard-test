<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_line_element_id = CValue::post("prescription_line_element_id");

if ($prescription_line_element_id && CValue::post("signee") == 1) {
  $guid = "CPrescriptionLineElement-$prescription_line_element_id";
  $ex_classes = CExClass::getExClassesForObject($guid, "signature", "required");
  echo CExClass::getJStrigger($ex_classes);
}

$do = new CDoObjectAddEdit("CPrescriptionLineElement", "prescription_line_element_id");
$do->doIt();

?>