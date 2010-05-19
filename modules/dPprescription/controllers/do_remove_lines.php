<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 6138 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

$prescription_id = CValue::post("prescription_id");
$praticien_id    = CValue::post("praticien_id");
$operation_id    = CValue::post("operation_id");
$lines = array();

$line_med = new CPrescriptionLineMedicament();
$line_med->signee = 0;
$line_med->praticien_id = $praticien_id;
$line_med->prescription_id = $prescription_id;
$lines["med"] = $line_med->loadMatchingList();

$line_comment = new CPrescriptionLineComment();
$line_comment->signee = 0;
$line_comment->praticien_id = $praticien_id;
$line_comment->prescription_id = $prescription_id;
$lines["comment"] = $line_comment->loadMatchingList();

$line_element = new CPrescriptionLineElement();
$line_element->signee = 0;
$line_element->praticien_id = $praticien_id;
$line_element->prescription_id = $prescription_id;
$lines["element"] = $line_element->loadMatchingList();

$prescription_line_mix = new CPrescriptionLineMix();
$prescription_line_mix->signature_prat = 0;
$prescription_line_mix->praticien_id = $praticien_id;
$prescription_line_mix->prescription_id = $prescription_id;
$lines["prescription_line_mix"] = $prescription_line_mix->loadMatchingList();

$current_user = new CMediusers();
$current_user->load($AppUI->user_id);
$current_user->isPraticien();

foreach($lines as $lines_by_type){
  foreach($lines_by_type as $_line){
  	$_line->getAdvancedPerms($current_user->_is_praticien, 0, 0, $operation_id);
		if($_line->_perm_edit){
	    $msg = $_line->delete();
	    CAppUI::displayMsg($msg, "$_line->_class_name-msg-delete");
		}
	}
}

echo CAppUI::getMsg();  
CApp::rip();

?>