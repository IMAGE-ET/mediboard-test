<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPportail
 * @version $Revision$
 * @author Fabien
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_forumtheme"  , null, TAB_READ);
$module->registerTab("vw_forumthread" , null, TAB_READ);
$module->registerTab("vw_forummessage", null, TAB_READ);

?>
