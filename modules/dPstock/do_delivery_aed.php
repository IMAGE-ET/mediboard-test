<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI;

if (isset($_POST['date']) && ($_POST['date'] == 'now')) {
  $_POST['date'] = mbDateTime();
}

$class = 'CProductDelivery';

$do = new CDoObjectAddEdit($class, 'delivery_id');
$do->createMsg = CAppUI::tr("msg-$class-create");
$do->modifyMsg = CAppUI::tr("msg-$class-modify");
$do->deleteMsg = CAppUI::tr("msg-$class-delete");
$do->doIt();

?>