<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_groups"  , null, TAB_READ);
$module->registerTab("vw_etab_externe", null, TAB_READ);

?>