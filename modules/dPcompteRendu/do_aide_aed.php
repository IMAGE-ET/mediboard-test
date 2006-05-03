<?php /* $Id: do_aide_aed.php,v 1.3 2005/04/10 15:51:29 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: 1.3 $
* @author Thomas Despoix
*/

require_once( $AppUI->getModuleClass('dPcompteRendu', 'aidesaisie'));

$obj = new CAideSaisie();
$msg = null;

if (!$obj->bind( $_POST )) {
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}

// detect if a delete operation has to be processed
$del = dPgetParam( $_POST, 'del', 0 );
if ($del) {
	// check canDelete
	if (!$obj->canDelete( $msg )) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	}

	// delete object
	if ($msg = $obj->delete()) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	} else {
    mbSetValueToSession("aide_id");
		$AppUI->setMsg( "Aide supprimée", UI_MSG_ALERT);
		$AppUI->redirect( "m=$m" );
	}
  
} else {
  // Store object
	if ($msg = $obj->store()) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$isNotNew = @$_POST['aide_id'];
		$AppUI->setMsg( $isNotNew ? 'Aide mise à jour' : 'Aide ajoutée', UI_MSG_OK);
	}
	$AppUI->redirect();
}
?>