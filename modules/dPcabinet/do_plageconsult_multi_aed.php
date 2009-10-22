<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

// Object binding
$obj = new CPlageconsult();
$obj->bind($_POST);

$del         = mbGetValueFromPost("del", 0 );
$repeat      = mbGetValueFromPost("_repeat", 0 );
$type_repeat = mbGetValueFromPost("_type_repeat", 1 );
   
if ($del) {
  // Suppression
  $obj->load();

  while ($repeat-- > 0) {
    if (!$obj->_id) {
      $AppUI->setMsg("Plage non trouvée", UI_MSG_ERROR);
    }
    else {
      if ($msg = $obj->delete()) {
        $AppUI->setMsg("Plage non supprimée", UI_MSG_ERROR);
        $AppUI->setMsg("Plage du $obj->date: $msg", UI_MSG_ERROR);
      } 
      else {
        $AppUI->setMsg("Plage supprimée", UI_MSG_OK);
      }
    }
    $obj->becomeNext();
  }
  
  mbSetValueToSession("plageconsult_id");

} else {
  // Modification des plages
  if($obj->_id != 0) { 
	  while ($repeat-- > 0) {    
	    if ($obj->_id) {
	      if ($msg = $obj->store()) {
	        $AppUI->setMsg("Plage non mise à jour", UI_MSG_ERROR);
	        $AppUI->setMsg("Plage du $obj->date: $msg", UI_MSG_ERROR);
	      } 
	      else {
	        $AppUI->setMsg("Plage mise à jour", UI_MSG_OK);
	      }      
	    } 
	    for ($i=1; $i <= $type_repeat; $i++) {
	      $obj->becomeNext();
	    }
	  }
  } 
  // Creation des plages
  else {
    while ($repeat-- > 0) {     
	    if ($msg = $obj->store()) {
	      $AppUI->setMsg("Plage non créée", UI_MSG_ERROR);
	      $AppUI->setMsg("Plage du $obj->date: $msg", UI_MSG_ERROR);
	    } 
	    else {
	      $AppUI->setMsg("Plage créée", UI_MSG_OK);
	    }
	    for ($i=1; $i <= $type_repeat; $i++) {
	      $obj->becomeNext();
	    }
	  }
  }
}

$AppUI->redirect("m=$m");

?>