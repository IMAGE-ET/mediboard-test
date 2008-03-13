<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CProductOrderItem', 'order_item_id');
$do->createMsg = 'Article créé';
$do->modifyMsg = 'Article modifié';
$do->deleteMsg = 'Article supprimé';
$do->doIt();

?>