<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $dPconfig;
set_time_limit(600);
ignore_user_abort(1);
ini_set("upload_max_filesize", $dPconfig["dPfiles"]["upload_max_filesize"]);
$do = new CFileAddEdit;
$do->doIt();
?>
