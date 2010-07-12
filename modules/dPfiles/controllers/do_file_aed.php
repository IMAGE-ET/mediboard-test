<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

set_time_limit(600);
ignore_user_abort(1);
CValue::setSession(CValue::postOrSession("private"));
ini_set("upload_max_filesize", CAppUI::conf("dPfiles upload_max_filesize"));
$do = new CFileAddEdit;
$do->doIt();
?>
