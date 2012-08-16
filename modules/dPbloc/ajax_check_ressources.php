<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$type      = CValue::get("type");
$object_id = CValue::get("object_id");

$besoins = new CBesoinRessource;
$besoins->$type = $object_id;
$besoins = $besoins->loadMatchingList();

// Vert : tout va bien
$color = "0a0";

if (count($besoins)) {
  $operation = reset($besoins)->loadRefOperation();
  $operation->loadRefPlageOp();
  
  $types_ressources = CMbObject::massLoadFwdRef($besoins, "type_ressource_id");
  
  foreach ($besoins as $_besoin) {
    $usage = $_besoin->loadRefUsage();
    
    if (!$usage->_id) {
      // Un usage absent, on passe en orange
      $color = "fb0";
      break;
    }
  }
  
  // Si la ressource d'un usage a une indispo ou est en conflit avec une autre intervention,
  // on passe en rouge
  if ($color != "fb0") {
    foreach ($besoins as $_besoin) {
      $usage = $_besoin->loadRefUsage();
      $ressource = $usage->loadRefRessource();
      
      // Check sur les indisponibilités
      $indispos = $ressource->loadRefsIndispos($operation->_datetime, $operation->_datetime );
      
      if (count($indispos)) {
        $color = "a00";
        break;
      }
      
      // Check sur les usages
      $usages = $ressource->loadRefsUsagesDateTime($operation->_datetime);
      
      // Il faut enlever l'usage éventuel associé au besoin qu'on est en train de parcourir
      if ($usage->_id) {
        unset($usages[$usage->_id]);
      }
      
      if (count($usages)) {
        $color = "a00";
        break;
      }
    }
  }
}

echo json_encode($color);
CApp::rip();
