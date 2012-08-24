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
  $deb_op = $operation->_datetime;
  $fin_op = mbAddDateTime($operation->temp_operation, $deb_op);
  
  CMbObject::massLoadFwdRef($besoins, "type_ressource_id");
  
  foreach ($besoins as $_besoin) {
    $usage = $_besoin->loadRefUsage();
    
    $ressource = new CRessourceMaterielle;
    $ressource->type_ressource_id = $_besoin->type_ressource_id;
    if ($usage->_id) {
      $ressource = $usage->loadRefRessource();
    }
    $type_ressource = $_besoin->loadRefTypeRessource();
    $nb_ressources = $type_ressource->countBackRefs("ressources_materielles");
    
    // Check sur les indisponibilités
    $indispos = $ressource->loadRefsIndispos($deb_op, $fin_op);
    
    // Check sur les besoins
    $besoins = $ressource->loadRefsBesoins($deb_op, $fin_op);
    unset($besoins[$_besoin->_id]);
    
    // Check sur les usages
    $usages = $ressource->loadRefsUsages($deb_op, $fin_op);
    if ($usage->_id) {
      unset($usages[$usage->_id]);
    }
    
    if (count($indispos) + count($besoins) + count($usages) >= $nb_ressources) {
      $color = "a00";
      break;
    }
  }
}

echo json_encode($color);
CApp::rip();
