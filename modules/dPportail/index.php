<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPportail
 * @version $Revision$
 * @author Fabien
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_forumtheme"  , TAB_READ);
$module->registerTab("vw_forumthread" , TAB_READ);
$module->registerTab("vw_forummessage", TAB_READ);

?>
