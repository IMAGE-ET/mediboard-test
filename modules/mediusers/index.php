<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_mediusers"  , null, TAB_READ);
$module->registerTab("vw_idx_functions"  , null, TAB_READ);
$module->registerTab("vw_idx_disciplines", null, TAB_READ);

?>