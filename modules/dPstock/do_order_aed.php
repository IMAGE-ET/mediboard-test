<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI;

// Current DateTime
if(isset($_POST['date_ordered'])){
  if($_POST['date_ordered'] == 'now'){
    $_POST['date_ordered'] = mbDateTime();
    
    // TODO: here : call to the ORDER function !!!!
    
  }
}

$do = new CDoObjectAddEdit('CProductOrder', 'order_id');
$do->createMsg = 'Commande créée';
$do->modifyMsg = 'Commande modifiée';
$do->deleteMsg = 'Commande supprimée';
$do->doIt();

?>