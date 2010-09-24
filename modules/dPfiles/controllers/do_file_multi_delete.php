<?php /* $Id: do_file_aed.php 9433 2010-07-12 13:33:10Z flaviencrochard $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 9433 $
* @author Romain Ollivier
*/

$object_guid = CValue::post("object_guid");
$object = CMbObject::loadFromGuid($object_guid);

// Chargement de la ligne à rendre active
foreach($object->loadBackRefs("files") as $_file) {
	$_POST["file_id"] = $_file->_id;
	$do = new CFileAddEdit;
	$do->redirect = null;
	$do->doIt();
}

echo CAppUI::getMsg();
CApp::rip();
?>
