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
$filter->nullifyEmptyFields();

// Récupéraration des doublon
$query = "SELECT COUNT(*) AS total, object_id, object_class, tag, id400,
    CAST(GROUP_CONCAT(id_sante400_id SEPARATOR ', ') AS CHAR) AS ids
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
$list = $filter->_spec->ds->loadList($query, 30);

// Récupération de la liste des classes disponibles
$listClasses = CApp::getInstalledClasses();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("filter", $filter);
$smarty->assign("listClasses", $listClasses);
$smarty->assign("list", $list);
$smarty->display("delete_duplicates.tpl");

?>