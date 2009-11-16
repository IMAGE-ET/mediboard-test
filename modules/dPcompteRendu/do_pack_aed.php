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

	// delete object
	if ($msg = $obj->delete()) {
		CAppUI::setMsg( $msg, UI_MSG_ERROR );
		CAppUI::redirect();
	} else {
    CValue::setSession("pack_id");
		CAppUI::setMsg( "Pack supprimée", UI_MSG_ALERT);
		CAppUI::redirect( "m=$m" );
	}
  
} else {
  // Store object
	if ($msg = $obj->store()) {
		CAppUI::setMsg( $msg, UI_MSG_ERROR );
	} else {
		$isNotNew = @$_POST['pack_id'];
		CAppUI::setMsg( $isNotNew ? 'Pack mis à jour' : 'Pack ajouté', UI_MSG_OK);
	}
	CAppUI::redirect();
}
?>