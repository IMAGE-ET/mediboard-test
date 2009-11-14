<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_files"       , TAB_READ);
$module->registerTab("vw_category"    , TAB_ADMIN);
$module->registerTab("files_integrity", TAB_ADMIN);
$module->registerTab("send_documents" , TAB_EDIT);
$module->registerTab("vw_stats"       , TAB_ADMIN);
?>