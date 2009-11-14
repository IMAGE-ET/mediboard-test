<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("view_identifiants" , TAB_READ);
$module->registerTab("stats_identifiants", TAB_READ);
$module->registerTab("synchro_sante400"  , TAB_EDIT);
$module->registerTab("view_marks"        , TAB_READ);
//$module->registerTab("easycom"         , TAB_EDIT);

?>