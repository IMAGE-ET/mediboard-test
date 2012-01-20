<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$do = new CDoObjectAddEdit("CFavoriCIM10");

// Amélioration des textes
$user = new CMediusers;
$user->load($_POST["favoris_user"]);
$for = " pour $user->_view";
$do->createMsg .= $for;
$do->modifyMsg .= $for;
$do->deleteMsg .= $for;

$do->redirect = null;
$do->doIt();

if (CAppUI::pref("new_search_cim10") == 1) {
  echo CAppUI::getMsg();
  CApp::rip();
}

?>