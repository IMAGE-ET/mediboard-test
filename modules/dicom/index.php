<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_sessions", TAB_READ);
//$module->registerTab("vw_idx_tables"  , TAB_ADMIN);