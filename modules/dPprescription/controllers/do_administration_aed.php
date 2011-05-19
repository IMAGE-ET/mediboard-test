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
}
$do->doRedirect();


?>