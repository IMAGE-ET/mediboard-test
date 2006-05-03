<?php /* ADMIN $Id: do_perms_aed.php,v 1.2 2005/02/16 14:52:17 mytto Exp $ */
global $m, $a;

$obj = new CPermission();

if (!$obj->bind( $_POST )) {
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}

$AppUI->setMsg( 'Permission' );

$del = isset($_POST['del']) ? $_POST['del'] : 0;
if ($del) {
	if ($msg = $obj->delete()) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	} else {
  	$AppUI->setMsg( "deleted", UI_MSG_ALERT, true );
	}
} else {
  $isNotNew = @$_POST['permission_id'];
	if ($msg = $obj->store()) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$AppUI->setMsg( $isNotNew ? 'updated' : 'added', UI_MSG_OK, true);
	}
}

$_SESSION[$m]["perm_id"] = null;
$AppUI->redirect("m=$m&a=viewuser&user_id=$obj->permission_user");
?>