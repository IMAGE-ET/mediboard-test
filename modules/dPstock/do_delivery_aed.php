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

$do = new CDoObjectAddEdit('CProductDelivery', 'delivery_id');
$do->createMsg = 'Administration créée';
$do->modifyMsg = 'Administration modifiée';
$do->deleteMsg = 'Administration supprimée';
$do->doIt();

?>