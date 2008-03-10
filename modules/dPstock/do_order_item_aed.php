<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI;

// Current DateTime
if(isset($_POST['date_received'])){
  if($_POST['date_received'] == 'now'){
    $_POST['date_received'] = mbDateTime();
  }
}

$do = new CDoObjectAddEdit('CProductOrderItem', 'order_item_id');
$do->createMsg = 'Article créé';
$do->modifyMsg = 'Article modifié';
$do->deleteMsg = 'Article supprimé';
$do->doIt();

?>