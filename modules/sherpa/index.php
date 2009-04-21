<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

//$module->registerTab("view_etablissements", null, TAB_READ);
$module->registerTab("view_malades"     , null, TAB_READ);
$module->registerTab("view_sejours"     , null, TAB_READ);
$module->registerTab("view_dossiers"    , null, TAB_READ);
$module->registerTab("view_droits"      , null, TAB_READ);
$module->registerTab("view_urgdos"      , null, TAB_READ);
$module->registerTab("view_urgdro"      , null, TAB_READ);
$module->registerTab("view_entccam"     , null, TAB_READ);
$module->registerTab("view_detccam"     , null, TAB_READ);
$module->registerTab("view_detngap"     , null, TAB_READ);
$module->registerTab("view_detcim"      , null, TAB_READ);
$module->registerTab("export_actes"     , null, TAB_EDIT);
$module->registerTab("import_actes"     , null, TAB_EDIT);
$module->registerTab("object_properties", null, TAB_READ);
$module->registerTab("view_idsherpa"    , null, TAB_EDIT);

?>