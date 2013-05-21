<?php

/**
 * Onglets du module dPcompteRendu
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_modeles"           , TAB_READ);
$module->registerTab("addedit_modeles"      , TAB_READ);
$module->registerTab("vw_idx_aides"         , TAB_READ);
$module->registerTab("vw_idx_listes"        , TAB_READ);
$module->registerTab("vw_idx_packs"         , TAB_READ);
$module->registerTab("vw_stats"             , TAB_ADMIN);
