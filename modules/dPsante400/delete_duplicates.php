<?php /* $Id: view_identifiants.php 12345 2011-06-03 12:55:42Z flaviencrochard $ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision: 12345 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$canSante400 = CModule::getCanDo("dPsante400");
$dialog = CValue::get("dialog");

// Chargement du filtre
$filter = new CIdSante400;
$filter->object_id    = CValue::getOrSession("object_id");
$filter->object_class = CValue::getOrSession("object_class", "CPatient");
$filter->_start_date  = CValue::getOrSession("_start_date", mbDateTime());
$filter->_end_date    = CValue::getOrSession("_end_date", mbDateTime());
$limit_duplicates     = CValue::getOrSession("limit_duplicates", 30);
$do_delete            = CValue::get("do_delete", false);

$filter->nullifyEmptyFields();

// Récupéraration des doublon
$query = "SELECT COUNT(*) AS total, object_id, object_class, tag, id400,
    CAST(GROUP_CONCAT(id_sante400_id SEPARATOR ', ') AS CHAR) AS ids, '' AS msg
  FROM id_sante400
  WHERE 1";
if($filter->object_id) {
  $query .= " AND object_id = '".$filter->object_id."'";
}
if($filter->object_class) {
  $query .= " AND object_class = '".$filter->object_class."'";
}
$query .= " AND last_update BETWEEN '".$filter->_start_date."' AND '".$filter->_end_date."'";
$query .= " GROUP BY object_id, tag, id400
  HAVING total > 1
  ORDER BY total DESC";
$list = $filter->_spec->ds->loadList($query, $limit_duplicates);

if($do_delete) {
  $idSante400 = new CIdSante400();
  foreach($list as &$duplicate) { 
    $where = array(
      "object_id"    => "= '".$duplicate["object_id"]."'",
      "object_class" => "= '".$duplicate["object_class"]."'",
      "tag"          => "= '".$duplicate["tag"]."'",
      "id400"        => "= '".$duplicate["id400"]."'",
    );
    
    $order = "last_update DESC";
    
    $listIdSante400 = $idSante400->loadList($where, $order);
    
    $survivor = reset($listIdSante400)->_id;
    
    foreach($listIdSante400 as $idSante400) {
      if($idSante400->_id != $survivor) {
        if($msg = $idSante400->delete()) {
          CAppUI::setMsg($msg, UI_MSG_WARNING);
        } else {
          CAppUI::setMsg("Identifiant supprimé", UI_MSG_OK);
        }
      }
    }
    $duplicate["msg"] = CAppUI::getMsg();
  }
}

// Récupération de la liste des classes disponibles
$listClasses = CApp::getInstalledClasses();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("filter"           , $filter);
$smarty->assign("limit_duplicates" , $limit_duplicates);
$smarty->assign("do_delete"        , $do_delete);
$smarty->assign("listClasses"      , $listClasses);
$smarty->assign("list"             , $list);
$smarty->display("delete_duplicates.tpl");

?>