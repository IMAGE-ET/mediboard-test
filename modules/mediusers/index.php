<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_mediusers"  , TAB_READ);
$module->registerTab("vw_idx_functions"  , TAB_READ);
$module->registerTab("vw_idx_disciplines", TAB_READ);

?>