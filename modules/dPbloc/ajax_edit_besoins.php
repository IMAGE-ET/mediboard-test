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
$deb_op = $operation->_datetime;
$fin_op  = mbAddDateTime($operation->temp_operation, $deb_op);

foreach ($besoins as $_besoin) {
  $type_ressource = $_besoin->loadRefTypeRessource();
  $nb_ressources = $type_ressource->countBackRefs("ressources_materielles");
  $_usage = $_besoin->loadRefUsage();
  
  // Côté protocole, rien à vérifier
  if ($type != "operation_id") {
    $_besoin->_color = "000";
    continue;
  }
  
  // Affichage de la couleur suivant l'état du besoin
  $_besoin->_color = "0a0";
  
  // S'il y a un usage, alors on peut vérifier si conflit avec :
  // - un autre usage
  // - une indispo
  // - un besoin
  // Dans ce cas, on passe en rouge
  if ($_usage->_id) {
    $ressource = $_usage->loadRefRessource();
    
    $_usages = $ressource->loadRefsUsages($deb_op, $fin_op);
    unset($_usages[$_usage->_id]);
    
    $_indispos = $ressource->loadRefsIndispos($deb_op, $fin_op);
    
    $_besoins = $ressource->loadRefsBesoins($deb_op, $fin_op);
    unset($_besoins[$_besoin->_id]);
    
    if (count($_usages) + count($_indispos) + count($_besoins) >= $nb_ressources) {
      $_besoin->_color = "a00";
    }
    
    continue;
  }
  
  // Sinon, on parcourt les ressources associées au type de ressource du besoin.
  $ressources = $type_ressource->loadRefsRessources();
  $_usages   = 0;
  $_indispos = 0;
  $_besoins  = 0;
  
  foreach ($ressources as $_ressource) {
    $_usages += count($_ressource->loadRefsUsages($deb_op, $fin_op));
    $_indispos += count($_ressource->loadRefsIndispos($deb_op, $fin_op));
  }
  
  // Pour compter les besoins, on ne le fait qu'une fois.
  // Car un besoin cible un type de ressource.
  // On décrémente d'une unité, car le besoin de la boucle est compté
  $_ressource = new CRessourceMaterielle;
  $_ressource->type_ressource_id = $type_ressource->_id;
  $_besoins = count($_ressource->loadRefsBesoins($deb_op, $fin_op)) - 1;
   
  if ($_usages + $_indispos + $_besoins >= $nb_ressources) {
    $_besoin->_color = "a00";
  }
}

$smarty = new CSmartyDP;

$smarty->assign("besoins", $besoins);
$smarty->assign("object_id", $object_id);
$smarty->assign("type"   , $type);
$smarty->assign("usage"  , $usage);

$smarty->display("inc_edit_besoins.tpl");
