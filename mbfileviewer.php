<?php /* $Id: */

/**
* @package Mediboard
* @version $Revision: $
* @author Romain Ollivier
*/

require "./includes/config.php";
require "./classes/ui.class.php";

session_name( 'dotproject' );
if (get_cfg_var( 'session.auto_start' ) > 0) {
	session_write_close();
}
session_start();
$AppUI =& $_SESSION['AppUI'];

require "{$AppUI->cfg['root_dir']}/includes/db_connect.php";

include "{$AppUI->cfg['root_dir']}/includes/main_functions.php";
include "{$AppUI->cfg['root_dir']}/includes/permissions.php";

$canRead = !getDenyRead( 'dPcabinet' );
if (!$canRead) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

$file_id = isset($_GET['file_id']) ? $_GET['file_id'] : 0;

require_once( $AppUI->getModuleClass('dPcabinet', 'files') );

if($file_id) {
  $file = new CFile();
  $file->load($file_id);

	// BEGIN extra headers to resolve IE caching bug (JRP 9 Feb 2003)
	// [http://bugs.php.net/bug.php?id=16173]
	header("Pragma: ");
	header("Cache-Control: ");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");  //HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	// END extra headers to resolve IE caching bug

	header("MIME-Version: 1.0");
	header( "Content-length: {$file->file_size}" );
	header( "Content-type: {$file->file_type}" );
	header( "Content-disposition: inline; filename={$file->file_name}" );
	if($file->file_consultation) {
    if(is_file("{$AppUI->cfg['root_dir']}/files/consultations/{$file->file_consultation}/{$file->file_real_filename}"))
      readfile( "{$AppUI->cfg['root_dir']}/files/consultations/{$file->file_consultation}/{$file->file_real_filename}" );
    else
      readfile( "{$AppUI->cfg['root_dir']}/files/consultations2/{$file->file_consultation}/{$file->file_real_filename}" );
	} else {
	  readfile( "{$AppUI->cfg['root_dir']}/files/operations/{$file->file_operation}/{$file->file_real_filename}" );
	}
} else {
	$AppUI->setMsg( "fileIdError", UI_MSG_ERROR );
	$AppUI->redirect();
}
?>
