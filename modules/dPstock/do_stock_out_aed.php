<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI;

if (isset($_GET['date']) && ($_GET['date'] == 'now')) {
	$_GET['date'] = mbDateTime();
}

$do = new CDoObjectAddEdit('CProductStock', 'stock_id');
$do->createMsg = 'Stock créé';
$do->modifyMsg = 'Stock modifié';
$do->deleteMsg = 'Stock supprimé';
$do->doIt();

?>