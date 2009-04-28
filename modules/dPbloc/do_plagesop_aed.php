<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Object binding
$obj = new CPlageOp();
$obj->bind($_POST);

$del         = dPgetParam( $_POST, "del", 0);
$repeat      = dPgetParam( $_POST, "_repeat", 0);
$type_repeat = dPgetParam( $_POST, "_type_repeat", 1);


// si l'id de l'objet est nul => creation
// si l'objet a un id, alors, modification


$body_msg = null;
$header   = array();
$msgNo    = null;

if ($del) {
  
  // Suppression

  $obj->load();

  $deleted = 0;
  $not_deleted = 0;
  $not_found = 0;

  while ($repeat--) {
    $msg = null;
    if ($obj->plageop_id) {
      if (!$msg = $obj->canDeleteEx()) {
        if ($msg = $obj->delete()) {
          $not_deleted++;
        } else {
          $msg = "plage supprimée";
          $deleted++;
        }
      } else {
        $not_deleted++;
      } 
    }
    else {
      $not_found++;
      $msg = "Impossible de supprimer, plage non trouvée";
    }
    
    $body_msg .= "<br />Plage du $obj->_day-$obj->_month-$obj->_year: " . $msg;
    
    $obj->becomeNext();
  }
  
  if ($deleted    ) $header [] = "$deleted plage(s) supprimée(s)";
  if ($not_deleted) $header [] = "$not_deleted plage(s) non supprimée(s)";
  if ($not_found  ) $header [] = "$not_found plage(s) non trouvée(s)";
  
  $msgNo = $deleted ? UI_MSG_ALERT : UI_MSG_ERROR;

  $_SESSION["dPbloc"]["id"] = null;
  
} else {

  //Modification
  
  if($obj->plageop_id!=0) {
    $created = 0;
    $updated = 0;
    $not_created = 0;
    $not_updated = 0;

    while ($repeat-- > 0) {
      $msg = null;
      if ($obj->plageop_id) {
        if ($msg = $obj->store()) {
          $not_updated++;
        } else {
          $msg = "plage mise à jour";
          $updated++;
        }
      } 
    
      $body_msg .= "<br />Plage du $obj->_day-$obj->_month-$obj->_year: " . $msg;
    
      for($i=1;$i<=$type_repeat;$i++){
        $obj->becomeNext();
      }
    }
  
    if ($created) $header [] = "$created plage(s) créée(s)";
    if ($updated) $header [] = "$updated plage(s) mise(s) à jour";
    if ($not_created) $header [] = "$not_created plage(s) non créée(s)";
    if ($not_updated) $header [] = "$not_created plage(s) non mise(s) à jour";
  
    $msgNo = ($not_created + $not_updated) ?
      (($not_created + $not_updated) ? UI_MSG_ALERT : UI_MSG_ERROR) :
      UI_MSG_OK;
  }
  // fin modification
  
  // debut creation
  else {
  
    $created = 0;
    $updated = 0;
    $not_created = 0;
    $not_updated = 0;

    while ($repeat-- > 0) {
      $msg = null;
      if ($obj->plageop_id) {
        if ($msg = $obj->store()) {
          $not_updated++;
        } else {
          $msg = "plage mise à jour";
          $updated++;
        }
      } else {
        if ($msg = $obj->store()) {
          $not_created++;
        } else {
          $msg = "plage créée";
          $created++;
        }
      }
    
      $body_msg .= "<br />Plage du $obj->_day-$obj->_month-$obj->_year: " . $msg;
    
      for($i=1;$i<=$type_repeat;$i++){
        $obj->becomeNext();
      }
    }
  
    if ($created) $header [] = "$created plage(s) créée(s)";
    if ($updated) $header [] = "$updated plage(s) mise(s) à jour";
    if ($not_created) $header [] = "$not_created plage(s) non créée(s)";
    if ($not_updated) $header [] = "$not_created plage(s) non mise(s) à jour";
  
    $msgNo = ($not_created + $not_updated) ?
      (($not_created + $not_updated) ? UI_MSG_ALERT : UI_MSG_ERROR) :
      UI_MSG_OK;
  }
}

$complete_msg = implode(" - ", $header);
if ($body_msg) {
  // Uncomment for more verbose
  // $complete_msg .= $body_msg; 
}

if( $otherm = dPgetParam($_POST, "otherm", 0) )
  $m = $otherm;

$AppUI->setMsg($complete_msg, $msgNo);
$AppUI->redirect("m=$m");

?>