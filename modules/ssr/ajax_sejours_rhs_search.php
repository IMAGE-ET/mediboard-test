<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$nda = CValue::get("nda");

$idex = new CIdSante400();
$where["object_class"] = "= 'CSejour'";
$where["id400"] = "LIKE '$nda%'";
$ideces = $idex->loadList($where, null, "100");
$sejours = array();
foreach ($ideces as $_idex) {
  $sejour = $_idex->loadTargetObject();
  $sejour->loadRefPatient()->loadIPP();
  $sejour->loadBackRefs("rhss", "date_monday");
  $sejours[$sejour->_id] = $sejour; 
} 

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejours", $sejours);
$smarty->display("inc_vw_rhs_sejour_search.tpl");

?>