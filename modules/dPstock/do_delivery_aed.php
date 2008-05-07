<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

if (isset($_POST['date']) && ($_POST['date'] == 'now')) {
  $_POST['date'] = mbDateTime();
}

$do = new CDoObjectAddEdit('CProductDelivery', 'delivery_id');
$do->doIt();

?>