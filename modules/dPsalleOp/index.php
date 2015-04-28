<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

global $can;

// Chargement de l'utilisateur courant
$user = CMediusers::get();
$user->isPraticien();

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_operations"  , TAB_READ);

if (!$user->_is_praticien || ($user->_is_praticien && $can->edit)) {
  $module->registerTab("vw_reveil"      , TAB_READ);
  //$module->registerTab("vw_soins_reveil", TAB_READ);
  $module->registerTab("vw_urgences"    , TAB_READ);
  $module->registerTab("vw_suivi_salles", TAB_READ);
  //$module->registerTab("vw_anesthesie"     , TAB_READ);
  if (CAppUI::conf('dPsalleOp CActeCCAM signature')) {
    $module->registerTab("vw_signature_actes", TAB_READ);
  }
  $module->registerTab("vw_interv_non_cotees", TAB_EDIT);
  $module->registerTab("vw_daily_check_traceability", TAB_READ);
  $module->registerTab("vw_daily_check_list_type"   , TAB_ADMIN);
  $module->registerTab("vw_daily_check_list_group"  , TAB_ADMIN);
}

if (CModule::getActive("vivalto")) {
  $module->registerTab("vw_dmi", TAB_READ);
}
