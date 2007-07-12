<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

$obj = new CPack();
$msg = null;

if (!$obj->bind( $_POST )) {
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}

// detect if a delete operation has to be processed
$del = dPgetParam( $_POST, 'del', 0 );
if ($del) {
	// check canDelete
	if ($msg = $obj->canDeleteEx()) {	
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	}

	// delete object
	if ($msg = $obj->delete()) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	} else {
    mbSetValueToSession("pack_id");
		$AppUI->setMsg( "Pack supprimée", UI_MSG_ALERT);
		$AppUI->redirect( "m=$m" );
	}
  
} else {
  // Store object
	if ($msg = $obj->store()) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$isNotNew = @$_POST['pack_id'];
		$AppUI->setMsg( $isNotNew ? 'Pack mis à jour' : 'Pack ajouté', UI_MSG_OK);
	}
	$AppUI->redirect();
}
?>