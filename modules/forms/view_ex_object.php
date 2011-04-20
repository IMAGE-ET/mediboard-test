<?php /* $Id: view_messages.php 7622 2009-12-16 09:08:41Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$reference_class = CValue::getOrSession("reference_class");
$reference_id    = CValue::getOrSession("reference_id");

$reference_classes = array(
  "CSejour", "CPatient",
);

if ($reference_class) {
	$reference = new $reference_class;
	if ($reference->load($reference_id)) {
		$reference->loadComplete();
	}
}
else {
	$reference = new CMbObject;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("reference_classes", $reference_classes);
$smarty->assign("reference_class",   $reference_class);
$smarty->assign("reference_id",      $reference_id);
$smarty->assign("reference",         $reference);
$smarty->display("view_ex_object.tpl");
