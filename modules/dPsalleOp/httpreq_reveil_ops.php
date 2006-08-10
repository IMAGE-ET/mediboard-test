<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPprotocoles
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

require_once($AppUI->getModuleClass("mediusers"));
require_once($AppUI->getModuleClass("mediusers"   , "functions"));
require_once($AppUI->getModuleClass("dPbloc"      , "salle"    ));
require_once($AppUI->getModuleClass("dPbloc"      , "plagesop" ));
require_once($AppUI->getModuleClass("dPplanningOp", "planning" ));

$date = mbGetValueFromGetOrSession("date", mbDate());
$hour = mbTime(null);

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
$listIdPlages = array();
foreach($plages as $key => $value) {
  $listIdPlages[] = "'".$value->salle_id."'";
}

$timing = array();

$listOps = new COperation;
$where = array();
if(count($listIdPlages)) {
  $where[] = "`plageop_id` IN(".implode(",", $listIdPlages).") OR (`plageop_id` IS NULL AND `date` = '$date')";
} else {
  $where[] = "`plageop_id` IS NULL AND `date` = '$date'";
}
$where["sortie_bloc"] = "IS NOT NULL";
$where["entree_reveil"] = "IS NULL";
$order = "sortie_bloc";
$listOps = $listOps->loadList($where, $order);
foreach($listOps as $key => $value) {
  $listOps[$key]->loadRefsFwd();
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
require_once($AppUI->getSystemClass ("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("listSalles"    , $listSalles              );
$smarty->assign("listAnesthType", dPgetSysVal("AnesthType"));
$smarty->assign("listAnesths"   , $listAnesths             );
$smarty->assign("listChirs"     , $listChirs               );
$smarty->assign("plages"        , $plages                  );
$smarty->assign("listOps"       , $listOps                 );
$smarty->assign("timing"        , $timing                  );
$smarty->assign("date"          , $date                    );
$smarty->assign("hour"          , $hour                    );

$smarty->display("inc_reveil_ops.tpl");

?>