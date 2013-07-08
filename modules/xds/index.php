<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage xds
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab('vw_test_xds'    , TAB_ADMIN);
$module->registerTab('vw_generate_xds', TAB_ADMIN);
$module->registerTab('vw_tools_xds'   , TAB_ADMIN);