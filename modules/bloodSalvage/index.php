<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage bloodSalvage
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

/**
 * On vérifie que le module est bien installé
 */
$module = CModule::getInstalled(basename(dirname(__FILE__)));

/**
 * Puis on crée l'index avec les vues du module vw_*
 */
$module->registerTab("vw_bloodSalvage",      TAB_READ);
$module->registerTab("vw_bloodSalvage_sspi", TAB_READ);
$module->registerTab("vw_stats",             TAB_READ);
$module->registerTab("vw_cellSaver",         TAB_EDIT);

if (CModule::getActive("dPqualite")) {
  $module->registerTab("vw_typeEi_manager", TAB_EDIT);
}
