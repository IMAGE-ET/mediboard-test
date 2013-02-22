<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage CDA
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab('vw_datatype'    , TAB_READ);
$module->registerTab('vw_highlightCDA', TAB_READ);