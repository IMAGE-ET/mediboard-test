<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$original_trigger_code = CValue::post("original_trigger_code");
$do_it                 = CValue::post("do_it");
$count                 = CValue::post("count", 10);
$auto                  = CValue::post("auto");

$request = new CRequest;
$request
  ->addSelect(array(
    "CAST(GROUP_CONCAT(movement_id) AS CHAR) AS ids",
    "original_trigger_code",
    "start_of_movement",
    "sejour_id",
  ))
  ->addTable("movement")
  ->addWhere(array(
    "original_trigger_code" => "= '$original_trigger_code'",
  ))
  ->addGroup(array(
    "original_trigger_code", 
    "start_of_movement", 
    "sejour_id",
  ))
  ->addHaving("COUNT(movement_id) > 1");
  
if ($do_it) {
  $request->setLimit($count);
}
  
$mov = new CMovement;
$query = $request->getRequest();
$list = $mov->_spec->ds->loadList($query);

if (!$do_it) {
  CAppUI::setMsg(count($list)." doublons à traiter");
}
else {
  foreach($list as $_mvt) {
    $ids = explode(",", $_mvt["ids"]);
    sort($ids); // IMPORTANT, must use the first movement created as a reference
    
    $first = new CMovement;
    $first->load($ids[0]);
    
    $second = new CMovement;
    $second->load($ids[1]);
    $tag = CIdSante400::getMatch($second->_class, $second->getTagMovement(), null, $second->_id);
    
    if ($tag->_id) {
      $tag->tag = "trash_$tag->tag";
      $tag->last_update = mbDateTime();
      $tag->store();
    }
    else {
      CAppUI::setMsg("Aucun tag sur mouvement #$second->_id");
    }
    
    $msg = $first->merge(array($second->_id => $second));
    
    if ($msg) {
      CAppUI::setMsg($msg, UI_MSG_WARNING);
    }
    else {
      CAppUI::setMsg("Mouvements fusionnés");
    }
  }
  
  if ($auto && count($list)) {
    CAppUI::js("removeMovementDuplicates()");
  }
}

echo CAppUI::getMsg();
CApp::rip();
