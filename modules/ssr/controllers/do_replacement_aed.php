<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$do = new CDoObjectAddEdit('CReplacement');

if ($sejour_ids = CMbArray::extract($_POST, "sejour_ids")) {
  $do->redirect = null;
  foreach (explode("-", $sejour_ids) as $sejour_id) {
  	$_POST["sejour_id"] = $sejour_id;
  	$do->doIt();
  }
  echo CAppUI::getMsg();
  CApp::rip();
}

$do->doIt();


?>