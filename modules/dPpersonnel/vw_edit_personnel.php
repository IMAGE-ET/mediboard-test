<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$emplacement = CValue::getOrSession("emplacement");
$_user_last_name = CValue::getOrSession("_user_last_name");
$_user_first_name = CValue::getOrSession("_user_first_name");


// Chargement du personnel selectionné
$personnel_id = CValue::getOrSession("personnel_id");
$personnel = new CPersonnel();
$personnel->load($personnel_id);
$personnel->loadRefs($personnel_id);
$personnel->loadBackRefs("affectations", "affect_id DESC", "0,20");
$personnel->countBackRefs("affectations");

// Chargement de la liste des affectations pour le filtre
$filter = new CPersonnel();
$where = array();
$ljoin["users"] = "users.user_id = personnel.user_id";

$order = "users.user_last_name";

if($emplacement){
  $where["emplacement"] = " = '$emplacement'";
  $filter->emplacement = $emplacement;
}
if($_user_last_name){
  $where["user_last_name"] = "LIKE '%$_user_last_name%'";
  $filter->_user_last_name = $_user_last_name;
}
if($_user_first_name){
  $where["user_first_name"] = "LIKE '%$_user_first_name%'";
  $filter->_user_first_name = $_user_first_name;
}

$filter->nullifyEmptyFields();
$personnels = $filter->loadGroupList($where, $order, null, null, $ljoin);
foreach ($personnels as $key => $_personnel){
  $_personnel->loadRefUser();
  $_personnel->countBackRefs("affectations");
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("personnels", $personnels );
$smarty->assign("personnel" , $personnel  );
$smarty->assign("filter", $filter);

$smarty->display("vw_edit_personnel.tpl");
?>