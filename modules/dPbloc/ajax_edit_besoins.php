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

CCanDo::checkEdit();

$type      = CValue::get("type");
$object_id = CValue::get("object_id");
$usage     = CValue::get("usage", 0);

$besoins = new CBesoinRessource;
$besoins->$type = $object_id;
$besoins = $besoins->loadMatchingList();
CMbObject::massLoadFwdRef($besoins, "type_ressource_id");

$operation = new COperation;
$operation->load($object_id);
$operation->loadRefPlageOp();
$hour_operation = mbTransformTime(null, $operation->temp_operation, "%H");
$min_operation  = mbTransformTime(null, $operation->temp_operation, "%M");
$fin_operation  = mbDateTime("+$hour_operation hours +$min_operation minutes", $operation->_datetime);

foreach ($besoins as $_besoin) {
  $type_ressource = $_besoin->loadRefTypeRessource();
  $_usage = $_besoin->loadRefUsage();
  
  if ($type != "operation_id") {
    $_besoin->_color = "000";
    continue;
  }
  
  // Affichage de la couleur suivant l'état du besoin
  $_besoin->_color = "0a0";
  
  // S'il y a un usage, alors on peut vérifier si conflit avec :
  // - un autre usage
  // - une indispo
  // Dans ce cas, on passe en rouge
  if ($_usage->_id) {
    $ressource = $_usage->loadRefRessource();
    
    $usages = $ressource->loadRefsUsagesDateTime($operation->_datetime);
    unset($usages[$_usage->_id]);
    
    if (count($usages)) {
      $_besoin->_color = "a00";
      continue;
    }
    
    $indispos = $ressource->loadRefsIndispos($operation->_datetime, $fin_operation);
    
    if (count($indispos)) {
      $_besoin->_color = "a00";
      continue;
    }
    
    continue;
  }
  
  // Sinon, on parcourt les ressources associées au type de ressource du besoin.
  // Si on on trouve un usage ou indispo en conflit, alors on passe en orange
  $ressources = $type_ressource->loadRefsRessources();
  $usages = 0;
  $indispos = 0;
  
  foreach ($ressources as $_ressource) {
    $usages += count($_ressource->loadRefsUsagesDateTime($operation->_datetime));
    $indispos += count($_ressource->loadRefsIndispos($operation->_datetime, $fin_operation));
  }
  
  if ($usages >= count($ressources) || $indispos >= count($ressources) || ($usages + $indispos) >= count($ressources)) {
    $_besoin->_color = "a00";
    continue;
  }
  
  if ($usages || $indispos) {
    $_besoin->_color = "fb0";
  }
}

$smarty = new CSmartyDP;

$smarty->assign("besoins", $besoins);
$smarty->assign("object_id", $object_id);
$smarty->assign("type"   , $type);
$smarty->assign("usage"  , $usage);

$smarty->display("inc_edit_besoins.tpl");
