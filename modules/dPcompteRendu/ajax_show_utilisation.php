<?php

/**
 * dPcompteRendu
 *  
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$compte_rendu_id = CValue::get("compte_rendu_id");

$compte_rendu = new CCompteRendu;
$compte_rendu->load($compte_rendu_id);

$modeles = array();

switch ($compte_rendu->type) {
  case "header":
    $modeles = $compte_rendu->loadBackRefs("modeles_headed", "nom");
    break;
  case "preface":
    $modeles = $compte_rendu->loadBackRefs("modeles_prefaced", "nom");
    break;
  case "body":
    $links = $compte_rendu->loadBackRefs("pack_links");
    $modeles = CMbObject::massLoadFwdRef($links, "pack_id");
    break;
  case "ending":
    $modeles = $compte_rendu->loadBackRefs("modeles_ended", "nom");
    break;
  case "footer":
    $modeles = $compte_rendu->loadBackRefs("modeles_footed", "nom");
    break;
}

$counts = array();
$ds = $compte_rendu->getDS();
if ($compte_rendu->type == "body") {
  $query = "SELECT `author_id`, COUNT(*) AS `total`
    FROM `compte_rendu`
    WHERE `modele_id` = '$compte_rendu->_id'
    GROUP BY `author_id`
    ORDER BY `total` DESC
  ";
  $counts = $ds->loadHashList($query);
}

$user = CMediusers::get();
$users = $user->loadAll(array_keys($counts));
foreach ($users as $_user) {
  $_user->loadRefFunction();
}

$smarty = new CSmartyDP;

$smarty->assign("modeles"     , $modeles);
$smarty->assign("counts"      , $counts);
$smarty->assign("users"       , $users);
$smarty->assign("compte_rendu", $compte_rendu);

$smarty->display("inc_vw_utilisation.tpl");
