<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

$obj = new CPack();
$obj->bind($_POST);

// detect if a delete operation has to be processed
$del = CValue::post('del', 0 );
if ($del) {
	// check canDelete
	if ($msg = $obj->canDeleteEx()) {	
		CAppUI::setMsg( $msg, UI_MSG_ERROR );
		CAppUI::redirect();
	}
	
  $pack_id = $obj->_id;
	
  // delete object
	
	if ($msg = $obj->delete()) {
		
		CAppUI::setMsg( $msg, UI_MSG_ERROR );
		CAppUI::redirect();
	} else {
    CValue::setSession("pack_id");
		CAppUI::setMsg( "Pack supprimée", UI_MSG_ALERT);
		
		$modeletopack = new CModeleToPack;
		$modeles = $modeletopack->deleteAllModelesFor($pack_id);

		CAppUI::redirect( "m=$m" );
	}
  
} else {
  // Store object
	if ($msg = $obj->store()) {
		CAppUI::setMsg( $msg, UI_MSG_ERROR );
	} else {
		$isNotNew = @$_POST['pack_id'];
		CAppUI::setMsg( $isNotNew ? 'Pack mis à jour' : 'Pack ajouté', UI_MSG_OK);
		
		$modeletopack = new CModeleToPack;
		
		if ($_del = CValue::post("_del")) {

			$where = array();
		  $where["pack_id"] = " = {$obj->_id}";
		  $where["modele_id"] = " = $_del";
			$modeletopack->loadObject($where);

			if($msg = $modeletopack->delete()) {
				CAppUI::setMsg( $msg, UI_MSG_ERROR );
			}
			CAppUI::redirect();
		  
		}
    if ($_new = CValue::post("_new")) {
		  $modeletopack->pack_id = $obj->_id;
      $modeletopack->modele_id = $_new; 
  
		  if ($msg = $modeletopack->store()) {
		    CAppUI::setMsg( $msg, UI_MSG_ERROR );
		  }
      CAppUI::redirect();
    }
	}
}
?>