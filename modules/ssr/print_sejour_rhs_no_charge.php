<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$sejour_ids  = explode("-", CValue::get("sejour_ids"));
$date_monday = CValue::get("date_monday");
$all_rhs     = CValue::get("all_rhs");

$where["sejour_id"] = CSQLDataSource::prepareIn($sejour_ids);
$where["date_monday"] = $all_rhs  ? ">= '$date_monday'" : "= '$date_monday'";

$order = "sejour_id, date_monday";

$rhs = new CRHS;
$sejours_rhs = $rhs->loadList($where, $order);

// Liste des catégories d'activité
$type_activite = new CTypeActiviteCdARR();
$types_activite = $type_activite->loadList();

$totaux = array();
foreach($sejours_rhs as $_rhs) {
  // Dépendances
  $dependances = $_rhs->loadRefDependances();
  if (!$dependances->_id) {
    $dependances->store();
  }
  
  $_rhs->loadRefSejour();
  $_rhs->buildTotaux();
  
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejours_rhs"    , $sejours_rhs);
$smarty->assign("read_only"      , true);

$smarty->display("print_sejour_rhs_no_charge.tpl");

?>