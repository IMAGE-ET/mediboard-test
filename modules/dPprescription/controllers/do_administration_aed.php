<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*/

$do = new CDoObjectAddEdit("CAdministration", "administration_id");
$do->doBind();

if ($do->_objBefore->planification == 1 || $do->_obj->planification == 1) {
  $do->createMsg = CAppUI::tr("CAdministration-planification-msg-create");
  $do->modifyMsg = CAppUI::tr("CAdministration-planification-msg-modify");
  $do->deleteMsg = CAppUI::tr("CAdministration-planification-msg-delete");
}

if (intval(CValue::read($do->request, 'del'))) {
  $do->doDelete();
}
else {
  $do->doStore();
	
	if (!CAppUI::conf("dPprescription CPrescription manual_planif") && $do->_obj->planification) {
		$prise_id = $do->_obj->prise_id;
		$prise = new CPrisePosologie();
		$prise->load($prise_id);
		
		$datetime = $do->_objBefore->_id ? $do->_objBefore->dateTime : $do->_obj->original_dateTime;
		
		$nb_hours = mbHoursRelative($datetime, $do->_obj->dateTime);
		
		if ($prise->nb_tous_les) {
			CAppUI::callbackAjax("PlanSoins.moveAllPlanifs", $prise_id, $do->_obj->object_id, $do->_obj->object_class, $datetime, $nb_hours, $prise->quantite);
		}
	}
}
$do->doRedirect();


?>