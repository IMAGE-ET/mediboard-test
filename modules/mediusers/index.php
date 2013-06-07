<?php

/**
 * Index
 *
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */


$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_mediusers"  , TAB_READ);
$module->registerTab("vw_idx_functions"  , TAB_READ);
$module->registerTab("vw_idx_disciplines", TAB_READ);
