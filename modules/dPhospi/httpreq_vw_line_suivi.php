<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$line_guid = CValue::get("line_guid");
$action    = CValue::get("action");
$without_del_form = CValue::getOrSession("without_del_form");

$user = new CMediusers();
$user->load(CAppUI::$instance->user_id);

// Chargement de la ligne de suivi de soins
$_suivi = CMbObject::loadFromGuid($line_guid);

if($_suivi instanceof CTransmissionMedicale){
	$_suivi->loadTargetObject();
	$_suivi->loadRefUser();
}

if($_suivi instanceof CObservationMedicale){
	$_suivi->loadRefUser();
}

if($_suivi instanceof CPrescriptionLineElement || $_suivi instanceof CPrescriptionLineComment){
	 $_suivi->countBackRefs("transmissions");
}

$_suivi->canEdit();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("_suivi"      , $_suivi);
$smarty->assign("isPraticien" , $user->isPraticien());
$smarty->assign("line_guid"   , $line_guid);
$smarty->assign("action"      , $action);
$smarty->assign("nodebug"     , true);
$smarty->assign("without_del_form", $without_del_form);
$smarty->display("inc_line_suivi.tpl");

?>