<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CProduct', 'product_id');
$do->createMsg = 'Produit créé';
$do->modifyMsg = 'Produit modifié';
$do->deleteMsg = 'Produit supprimé';
$do->doIt();

?>