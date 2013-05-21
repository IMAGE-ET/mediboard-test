<?php

/**
 * dPcim10
 *
 * @category Cim10
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_find_code"  , TAB_READ);
$module->registerTab("vw_full_code"  , TAB_READ);
$module->registerTab("vw_idx_chapter", TAB_READ);
$module->registerTab("vw_idx_favoris", TAB_READ);
