<?php /* $Id: do_edit_mat.php,v 1.2 2005/05/04 17:32:10 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision: 1.2 $
* @author Romain Ollivier
*/

$id = dPgetParam($_GET, 'id', 0);
$value = dPgetParam($_GET, 'value', 'n');
if($id) {
  $sql = "UPDATE operations
          SET commande_mat = '$value'
          WHERE operation_id = '$id'";
  $result = db_exec($sql);
}

$AppUI->redirect();

?>