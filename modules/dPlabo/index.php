<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Labo
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_edit_prescriptions"   , TAB_READ);
$module->registerTab("vw_resultats"            , TAB_READ);
$module->registerTab("add_pack_exams"          , TAB_READ);
$module->registerTab("vw_edit_packs"           , TAB_READ);
$module->registerTab("vw_edit_catalogues"      , TAB_EDIT);
$module->registerTab("vw_edit_examens"         , TAB_EDIT);
$module->registerTab("vw_edit_idLabo"          , TAB_EDIT);
