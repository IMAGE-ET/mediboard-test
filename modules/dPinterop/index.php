<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Thomas Despoix
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("import_ami"         , "Import AMI"         , TAB_READ);
$module->registerTab("import_dermato"     , "Import Dermato"     , TAB_READ);
$module->registerTab("import_orl"         , "Import ORL"        , TAB_READ);
$module->registerTab("test"               , "Test"               , TAB_READ);
?>