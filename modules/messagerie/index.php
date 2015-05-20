<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage dPportail
 * @version $Revision$
 * @author Fabien
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_messagerie", TAB_READ);
$module->registerTab("vw_list_accounts", TAB_ADMIN);
