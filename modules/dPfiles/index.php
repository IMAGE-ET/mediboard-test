<?php
/**
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_files"       , TAB_READ);
$module->registerTab("vw_category"    , TAB_ADMIN);
$module->registerTab("files_integrity", TAB_ADMIN);
$module->registerTab("send_documents" , TAB_EDIT);
$module->registerTab("vw_stats"       , TAB_ADMIN);
