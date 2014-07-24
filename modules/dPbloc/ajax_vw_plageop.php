<?php 

/**
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$plage_id = CValue::get("plage_id");

$object = new CPlageOp();
$object->load($plage_id);
$object->loadRefsNotes();

$object->loadRefChir()->loadRefFunction();
$object->loadRefAnesth()->loadRefFunction();
$object->loadRefSpec();

$object->loadRefsOperations();
$object->loadRefSalle();

foreach ($object->_ref_operations as $_op) {
  $_op->loadRefPatient()->loadRefPhotoIdentite();
}

// smarty
$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->display("inc_vw_plageop.tpl");