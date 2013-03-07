<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem
 */

CCanDo::checkRead();

$_date_min = CValue::getOrSession('_date_min', CMbDT::dateTime("-7 day"));
$_date_max = CValue::getOrSession('_date_max', CMbDT::dateTime("+1 day"));

$session = new CDicomSession(null);

$session->_date_min = $_date_min;
$session->_date_max = $_date_max;

$smarty = new CSmartyDP();
$smarty->assign("session", $session);
$smarty->assign("page", 0);
$smarty->display("vw_idx_sessions.tpl");