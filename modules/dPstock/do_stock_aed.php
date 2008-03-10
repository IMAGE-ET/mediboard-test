<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CProductStock', 'stock_id');
$do->createMsg = 'Stock créé';
$do->modifyMsg = 'Stock modifié';
$do->deleteMsg = 'Stock supprimé';
$do->doIt();

?>