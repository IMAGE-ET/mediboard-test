<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision$
* @author Romain OLLIVIER
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("view_planning", TAB_READ);
$module->registerTab("edit_planning", TAB_EDIT);
$module->registerTab("view_compta"  , TAB_EDIT);

?>