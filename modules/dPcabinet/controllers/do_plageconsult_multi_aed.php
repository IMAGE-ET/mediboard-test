<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

// Object binding
$obj = new CPlageconsult();
$obj->bind($_POST);

$del    = CValue::post("del"    , 0);
$repeat = min(CValue::post("_repeat", 0), 100);
   
if ($del) {
  // Suppression des plages
  $obj->load();
  while ($repeat > 0) {
    if (!$obj->_id) {
      CAppUI::setMsg("Plage non trouvée", UI_MSG_ERROR);
    }
    else {
      if ($msg = $obj->delete()) {
        CAppUI::setMsg("Plage non supprimée", UI_MSG_ERROR);
        CAppUI::setMsg("Plage du $obj->date: $msg", UI_MSG_ERROR);
      }
      else {
        CAppUI::setMsg("Plage supprimée", UI_MSG_OK);
      }
    }
    $repeat -= $obj->becomeNext();
  }
  
  CValue::setSession("plageconsult_id");

}
else {
  // Modification des plages
  if ($obj->_id != 0) { 
    while ($repeat > 0) {    
      if ($obj->_id) {
        if ($msg = $obj->store()) {
          CAppUI::setMsg("Plage non mise à jour", UI_MSG_ERROR);
          CAppUI::setMsg("Plage du $obj->date: $msg", UI_MSG_ERROR);
        }
        else {
          CAppUI::setMsg("Plage mise à jour", UI_MSG_OK);
        }      
      } 
      $repeat -= $obj->becomeNext();
    }
  } 
  // Creation des plages
  else {
    while ($repeat > 0) {     
      if ($msg = $obj->store()) {
        CAppUI::setMsg("Plage non créée", UI_MSG_ERROR);
        CAppUI::setMsg("Plage du $obj->date: $msg", UI_MSG_ERROR);
      }
      else {
        CAppUI::setMsg("Plage créée", UI_MSG_OK);
      }
      $repeat -= $obj->becomeNext();
    }
  }
}

CAppUI::redirect("m=$m");

?>