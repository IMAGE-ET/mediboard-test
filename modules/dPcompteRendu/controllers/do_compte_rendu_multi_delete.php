<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPcompteRendu
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$object_guid = CValue::post("object_guid");
$object = CMbObject::loadFromGuid($object_guid);

// Chargement de la ligne � rendre active
foreach($object->loadBackRefs("documents") as $_doc) {
  $_POST["compte_rendu_id"] = $_doc->_id;
  $_POST["del"] = "1";
  $do = new CDoObjectAddEdit("CCompteRendu");
  $do->redirect = $do->redirectDelete = null;
  $do->doIt();
}

echo CAppUI::getMsg();
CApp::rip();