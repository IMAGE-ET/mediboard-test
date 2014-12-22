<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkRead();
$uf_id = CValue::getOrSession("uf_id");

$uf = new CUniteFonctionnelle();
$uf->load($uf_id);

if ($uf->type == "hebergement") {
  $type_affectations = array( "CLit" => array(), "CChambre" => array(),  "CService" => array());
}
elseif ($uf->type == "soins") {
  $type_affectations = array( "CService" => array());
}
elseif ($uf->type == "medicale") {
  $type_affectations = array("CMediusers" => array(), "CFunctions" => array());
}

foreach ($type_affectations as $type => $tab_type) {
  $affect = new CAffectationUniteFonctionnelle();
  $affect->uf_id = $uf_id;
  $affect->object_class = $type;
  $affectations = $affect->loadMatchingList();
  foreach ($affectations as $_affect) {
    /* @var CAffectationUniteFonctionnelle $_affect*/
    $_affect->loadTargetObject();
  }
  $type_affectations[$type] = $affectations;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("uf"                , $uf);
$smarty->assign("type_affectations" , $type_affectations);

$smarty->display("vw_stats_uf.tpl");
