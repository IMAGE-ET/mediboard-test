<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 6138 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

$prescription_id = mbGetValueFromPost("prescription_id");
$praticien_id    = mbGetValueFromPost("praticien_id");

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

$perfusion = new CPerfusion();
$perfusion->signature_prat = 0;
$perfusion->praticien_id = $praticien_id;
$perfusion->prescription_id = $prescription_id;
$lines["perfusion"] = $perfusion->loadMatchingList();

foreach($lines as $lines_by_type){
  foreach($lines_by_type as $_line){
    $msg = $_line->delete();
    $AppUI->displayMsg($msg, "$_line->_class_name-msg-delete");
  }
}

CApp::rip();

?>