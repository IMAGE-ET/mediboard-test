<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_affectations_pers", null, TAB_READ);
$module->registerTab("vw_edit_personnel", null, TAB_READ);
$module->registerTab("vw_affectations_multiples", null, TAB_EDIT);
?>