<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getModuleClass("dPfiles", "files"));
require_once($AppUI->getModuleClass("dPfiles", "do_file_aed"));

$do = new CFileAddEdit;
$do->doIt();
?>
