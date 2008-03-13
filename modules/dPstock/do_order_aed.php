<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CProductOrder', 'order_id');
$do->createMsg = 'Commande créée';
$do->modifyMsg = 'Commande modifiée';
$do->deleteMsg = 'Commande supprimée';
$do->doIt();

?>