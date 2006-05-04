<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

require_once( $AppUI->getModuleClass("dPcompteRendu", "listeChoix"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CListeChoix", "liste_choix_id");
$do->createMsg = "Liste créée";
$do->modifyMsg = "Liste modifiée";
$do->deleteMsg = "Liste supprimée";
$do->doBind();
if (intval(dPgetParam($_POST, 'del'))) {
  $do->doDelete();
  $do->redirect = "m=dPcompteRendu&liste_id=0";
} else {
  $do->doStore();
  $do->redirect = "m=dPcompteRendu&liste_id=".$do->_obj->liste_choix_id;
}
$do->doRedirect();


/*
$obj = new CListeChoix();
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
    mbSetValueToSession("liste_id");
		$AppUI->setMsg( "Liste supprimée", UI_MSG_ALERT);
		$AppUI->redirect( "m=$m" );
	}
  
} else {
  // Store object
	if ($msg = $obj->store()) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$isNotNew = @$_POST['liste_choix_id'];
		$AppUI->setMsg( $isNotNew ? 'Liste mise à jour' : 'Liste ajoutée', UI_MSG_OK);
	}
	$AppUI->redirect();
}*/
?>