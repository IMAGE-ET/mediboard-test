<?php 

/**
 * $Id$
 *  
 * @category dPplanningOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();
$page = CValue::get("page", "0");
$nom  = CValue::get("nom");

$where = array();
$where["group_id"] = " = '".CGroups::loadCurrent()->_id."'";
if ($nom) {
  $where["nom"]    = " LIKE '%$nom%'";
}

$libelle    = new CLibelleOp();
$total_libs = $libelle->countList($where);
$libelles   = $libelle->loadGroupList($where, "nom", "$page, 50");


$smarty = new CSmartyDP();
$smarty->assign('libelles'   , $libelles);
$smarty->assign("nom"        , $nom);
$smarty->assign("page"       , $page);
$smarty->assign("total_libs" , $total_libs);
$smarty->display("inc_search_libelle.tpl");