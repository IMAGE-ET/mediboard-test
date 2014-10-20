<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage importTools
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$module = CModule::getInstalled(basename(__DIR__));

$module->registerTab('vw_database_explorer', TAB_ADMIN);