<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$do = new CDoObjectAddEdit("CFavoriCCAM", "favoris_id");

// Amélioration des textes
if ($favori_user = CValue::post("favoris_user")) {
	$user = new CMediusers;
	$user->load($favori_user);
	$for = " pour $user->_view";
	$do->createMsg .= $for;
	$do->modifyMsg .= $for;
	$do->deleteMsg .= $for;
}

$do->redirect = null;
$do->doIt();

?>