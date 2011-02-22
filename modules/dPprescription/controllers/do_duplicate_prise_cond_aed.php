<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prise_id = CValue::post("prise_id");

$prise = new CPrisePosologie();
$prise->load($prise_id);

$prise->_id = "";
$prise->condition = "";
$prise->datetime = mbDateTime();

$msg = $prise->store();

CApp::rip();
?>