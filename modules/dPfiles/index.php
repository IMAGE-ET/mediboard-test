<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_files"       , null, TAB_READ);
$module->registerTab("vw_category"    , null, TAB_ADMIN);
$module->registerTab("files_integrity", null, TAB_ADMIN);

?>