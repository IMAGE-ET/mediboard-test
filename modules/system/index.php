<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("view_modules",           TAB_ADMIN);
$module->registerTab("idx_messages",           TAB_READ);
$module->registerTab("object_merger",          TAB_ADMIN);
$module->registerTab("view_history",           TAB_EDIT);
$module->registerTab("view_access_logs",       TAB_READ);
$module->registerTab("view_long_request_logs", TAB_READ);
$module->registerTab("view_ressources_logs",   TAB_READ);
$module->registerTab("vw_idx_redirections",    TAB_ADMIN);
$module->registerTab("view_translations",      TAB_EDIT);
$module->registerTab("view_network_address",   TAB_EDIT);
$module->registerTab("idx_view_senders",       TAB_EDIT);
$module->registerTab("about",                  TAB_READ);