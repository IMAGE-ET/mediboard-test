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

$do = new CDoObjectAddEdit('CProductStockOut', 'stock_out_id');
$do->createMsg = 'Déstockage créé';
$do->modifyMsg = 'Déstockage modifié';
$do->deleteMsg = 'Déstockage supprimé';
$do->doIt();

?>