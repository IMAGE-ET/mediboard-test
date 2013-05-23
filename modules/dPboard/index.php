<?php

/**
 * dPboard
 *
 * @category Board
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_week", TAB_READ);
$module->registerTab("vw_day", TAB_READ);
$module->registerTab("vw_idx_sejour", TAB_READ);

if (CModule::getActive("dPprescription")) {
  $module->registerTab("vw_bilan_prescription", TAB_READ);
  $module->registerTab("vw_bilan_transmissions", TAB_READ);
}

$module->registerTab("vw_interv_non_cotees", TAB_EDIT);
$module->registerTab("vw_stats", TAB_READ);
$module->registerTab("vw_agenda", TAB_READ);
