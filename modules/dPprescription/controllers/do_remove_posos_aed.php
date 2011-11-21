<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 6138 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$line_guid = CValue::post("line_guid");

$line = CMbObject::loadFromGuid($line_guid);
$line->loadRefsPrises();

foreach($line->_ref_prises as $_prise){
	$_prise->delete();
}

CApp::rip();

?>