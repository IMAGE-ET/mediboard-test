<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$date = mbGetValueFromGetOrSession("date", mbDate());
$hour = mbTime(null);
$date_now = mbDate();
$modif_operation = $date>=$date_now;

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes();

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens();


// Selection des salles
$listSalles = new CSalle;
$listSalles = $listSalles->loadList();

// Selection des plages opératoires de la journée
$plages = new CPlageOp;
$where = array();
$where["date"] = "= '$date'";
$plages = $plages->loadList($where);

$timing = array();

$listOps = new COperation;
$where = array();
$where[] = "`plageop_id` ".db_prepare_in(array_keys($plages))." OR (`plageop_id` IS NULL AND `date` = '$date')";
$where["sortie_salle"] = "IS NOT NULL";
$where["entree_reveil"] = "IS NULL";
$order = "sortie_salle";
$listOps = $listOps->loadList($where, $order);
foreach($listOps as $key => $value) {
  $listOps[$key]->loadRefsFwd();
  if($listOps[$key]->_ref_sejour->type == "exte"){
    unset($listOps[$key]);
    continue;
  }
  $listOps[$key]->_ref_plageop->loadRefsFwd();
  $listOps[$key]->_ref_sejour->loadRefsFwd();
  //Tableau des timmings
  $timing[$key]["entree_reveil"] = array();
  $timing[$key]["sortie_reveil"] = array();
  foreach($timing[$key] as $key2 => $value2) {
    for($i = -10; $i < 10 && $value->$key2 !== null; $i++) {
      $timing[$key][$key2][] = mbTime("+ $i minutes", $value->$key2);
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listSalles"    , $listSalles              );
$smarty->assign("listAnesths"   , $listAnesths             );
$smarty->assign("listChirs"     , $listChirs               );
$smarty->assign("plages"        , $plages                  );
$smarty->assign("listOps"       , $listOps                 );
$smarty->assign("timing"        , $timing                  );
$smarty->assign("date"          , $date                    );
$smarty->assign("hour"          , $hour                    );
$smarty->assign("modif_operation", $modif_operation);

$smarty->display("inc_reveil_ops.tpl");

?>