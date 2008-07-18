<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

if (isset($_POST['date_dispensation']) && ($_POST['date_dispensation'] == 'now')) {
	$_POST['date_dispensation'] = mbDateTime();
}

$do = new CDoObjectAddEdit('CProductDelivery', 'delivery_id');
$do->doIt();

?>