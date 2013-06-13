<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage dPportail
 * @version $Revision$
 * @author Fabien
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

//internal
$module->registerTab("vw_list_internalMessages", TAB_READ);

//external
$module->registerTab("vw_list_externalMessages", TAB_READ);
