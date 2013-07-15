<?php

/**
 * maternite
 *  
 * @category maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_admissions"      , TAB_READ);
$module->registerTab("vw_grossesses"      , TAB_READ);
$module->registerTab("vw_placement"       , TAB_READ);
$module->registerTab("vw_consultations"   , TAB_READ);

?>