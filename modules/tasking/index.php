<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage todo
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab('vw_tasks',           TAB_READ);
//$module->registerTab('vw_ticket_requests', TAB_READ);
$module->registerTab('vw_import',          TAB_READ);