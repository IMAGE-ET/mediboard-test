<?php /* $Id: do_planning_aed.php,v 1.28 2005/11/16 21:00:23 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: 1.28 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getSystemClass("doobjectaddedit"));
require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );

if($chir_id = dPgetParam( $_POST, 'chir_id', null))
  mbSetValueToSession('chir_id', $chir_id);
  
$do = new CDoObjectAddEdit("COperation", "operation_id");
$do->doBind();

if (intval(dPgetParam($_POST, 'del'))) {
  if($do->_obj->plageop_id && $do->_obj->pat_id) {
    mbSetValueToSession("operation_id");
    $do->deleteMsg = "Opération supprimée";
    $do->redirectDelete = "m=$m&tab=vw_edit_planning";
  } elseif($do->_obj->pat_id) {
    mbSetValueToSession("hospitalisation_id");
    $do->deleteMsg = "Hospitalisation supprimée";
    $do->redirectDelete = "m=$m&tab=vw_edit_hospi";
  } else {
    mbSetValueToSession("protocole_id");
    $do->deleteMsg = "Protocole supprimé";
    $do->redirectDelete = "m=$m&tab=vw_add_protocole";
  }
  $do->doDelete();
} else {
  if($do->_obj->plageop_id && $do->_obj->pat_id) {
    $do->modifyMsg = "Opération modifiée";
    $do->createMsg = "Opération créée";
  } elseif($do->_obj->pat_id) {
    $do->modifyMsg = "Hospitalisation modifiée";
    $do->createMsg = "Hospitalisation créée";
  } else {
    $do->modifyMsg = "Protocole modifié";
    $do->createMsg = "Protocole créé";
  }
  $do->doStore();
  // Pour que la redirection prenne vraiment l'objet en compte et pas que les valeurs du POST
  // -> on reload l'objet
  $do->_obj->load($do->_obj->operation_id);
  if($otherm = dPgetParam( $_POST, 'otherm', 0))
    $m = $otherm;
  if($m == "dPhospi")
    $do->redirectStore = "m=$m#operation".$do->_obj->operation_id;
  elseif($do->_obj->plageop_id && $do->_obj->pat_id)
    $do->redirectStore = "m=$m&operation_id=".$do->_obj->operation_id;
  elseif($do->_obj->pat_id)
    $do->redirectStore = "m=$m&hospitalisation_id=".$do->_obj->operation_id;
  else
    $do->redirectStore = "m=$m&protocole_id=".$do->_obj->operation_id;
}
$do->doRedirect();

?>