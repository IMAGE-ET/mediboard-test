<?php  /** $Id: vw_plan_etage.php  $ **/

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 7320 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Récupération des paramètres
$service_id   = CValue::postOrSession("service_id");

//Chargement de tous les services
$service = new CService();
$where = array();
$where["group_id"]   = "= '".CGroups::loadCurrent()->_id."'";
$where["cancelled"]  = "= '0'";
$services = $service->loadGroupList($where, "nom ASC");

$service_selectionne = new CService();
$where["service_id"] = " = '$service_id'";
$service_selectionne->loadObject($where);

$chambre = new CChambre();
$where = array();
$where["service_id"] = " = '$service_selectionne->_id'";
$chambres_service = $chambre->loadGroupList($where);

$ljoin = array();
$ljoin["service"] = "service.service_id = chambre.service_id";
$ljoin["emplacement"] = "emplacement.chambre_id = chambre.chambre_id";
$where=array();
$where["emplacement.plan_x"] = "IS NOT NULL";
$where["emplacement.plan_y"] = "IS NOT NULL";
$where["service.service_id"] = " = '$service_id'";
$where["service.group_id"] = "= '".CGroups::loadCurrent()->_id."'";
$chambre_places = $chambre->loadGroupList($where, null, null, null, $ljoin);

$chambres_non_placees = $chambres_service;
if (count($chambre_places)) {
  $where=array();
  $where["service_id"] = " = '$service_id'";
  $where["chambre_id"] = " NOT ". CSQLDataSource::prepareIn(array_keys($chambre_places));
  $chambres_non_placees = $chambre->loadGroupList($where);
}

foreach ($chambres_non_placees as $ch) {
  $ch->loadRefsFwd();
  $ch->loadRefEmplacement();
}

$conf_nb_colonnes = CAppUI::conf("dPhospi nb_colonnes_vue_topologique");

$grille = array_fill(0, $conf_nb_colonnes, array_fill(0, $conf_nb_colonnes, 0));

if ($service_id!="") {
  foreach ($chambre_places as $chambre) {
    $chambre->loadRefsFwd();
    $emplacement = $chambre->loadRefEmplacement();
    $grille[$emplacement->plan_y][$emplacement->plan_x] = $chambre;
    if ($emplacement->hauteur-1) {
      for ($a = 0; $a <= $emplacement->hauteur-1; $a++) {
        if ($emplacement->largeur-1) {
          for ($b = 0; $b <= $emplacement->largeur-1; $b++) {
            if ($b!=0) {
              unset($grille[$emplacement->plan_y+$a][$emplacement->plan_x+$b]);
            }
            elseif ($a!=0) {
              unset($grille[$emplacement->plan_y+$a][$emplacement->plan_x+$b]);
            }
          }
        }
        elseif ($a < $emplacement->hauteur-1) {
          $c = $a+1;
          unset($grille[$emplacement->plan_y+$c][$emplacement->plan_x]);
        }
      }
    }
    elseif ($emplacement->largeur-1) {
      for ($b = 1; $b <= $emplacement->largeur-1; $b++) {
        unset($grille[$emplacement->plan_y][$emplacement->plan_x+$b]);
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("services"  , $services);
$smarty->assign("chambres_non_placees"  , $chambres_non_placees);
$smarty->assign("service_id"            , $service_id);
$smarty->assign("service_selectionne"   , $service_selectionne);
$smarty->assign("grille"                , $grille);

$smarty->display("vw_plan_etage.tpl");
?>