<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CProductStockOut', 'stock_out_id');
$do->createMsg = 'Déstockage créé';
$do->modifyMsg = 'Déstockage modifié';
$do->deleteMsg = 'Déstockage supprimé';
$do->doIt();

?>