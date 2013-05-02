<?php

/**
 * $Id$
 *
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_edit_users", TAB_EDIT);
$module->registerTab("edit_perms"   , TAB_EDIT);
$module->registerTab("edit_prefs"   , TAB_EDIT);
$module->registerTab("vw_all_perms" , TAB_READ);
$module->registerTab("vw_edit_tokens", TAB_EDIT);
