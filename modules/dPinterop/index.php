<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Thomas Despoix
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("import_ami"         , TAB_READ);
$module->registerTab("import_dermato"     , TAB_READ);
$module->registerTab("import_orl"         , TAB_READ);
$module->registerTab("test"               , TAB_READ);
?>