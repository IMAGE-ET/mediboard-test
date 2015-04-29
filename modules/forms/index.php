<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage forms
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$module = CModule::getInstalled(basename(__DIR__));

$module->registerTab("view_ex_class",      TAB_EDIT);
$module->registerTab("view_ex_list",       TAB_EDIT);
$module->registerTab("view_ex_concept",    TAB_EDIT);
$module->registerTab("view_ex_class_category", TAB_EDIT);
$module->registerTab("view_ex_object_explorer", TAB_EDIT);
$module->registerTab("vw_import_ex_class", TAB_EDIT);

/*
$module->registerTab("view_import",        TAB_ADMIN);
$module->registerTab("view_import_fields", TAB_ADMIN);*/