<?php 

/**
 * $Id$
 *  
 * @category Maternité
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$patient_id   = CValue::getOrSession("patient_id");
$object_guid  = CValue::get("object_guid");

$smarty = new CSmartyDP();

$smarty->assign("patient_id"  , $patient_id);
$smarty->assign("object_guid" , $object_guid);

$smarty->display("inc_bind_allaitement.tpl");