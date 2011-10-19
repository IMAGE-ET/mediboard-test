<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$list = CValue::post("d", array());

foreach($list as $_id => $_data) {
	$do = new CDoObjectAddEdit('CProductDelivery');
	unset($do->request);
	$do->request = $_data;
	$do->redirect = null;
	$do->doIt();
}

echo CAppUI::getMsg();
CApp::rip();
