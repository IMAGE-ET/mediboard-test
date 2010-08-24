<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$rhs_date_monday = CValue::get('rhs_date_monday');

$rhs = new CRHS();

$where = array();
$where['date_monday'] = " = '$rhs_date_monday'";
$sejours_rhs = $rhs->loadList($where);
foreach ($sejours_rhs as $_rhs) {
  $_rhs->loadRefSejour();
}

$where['facture'] = " = '0'";
$count_sej_rhs_no_charge = $rhs->countList($where);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejours_rhs"            , $sejours_rhs);
$smarty->assign("count_sej_rhs_no_charge", $count_sej_rhs_no_charge);
$smarty->assign("rhs_date_monday"        , $rhs_date_monday);
$smarty->assign("read_only"              , true);

$smarty->display("inc_vw_rhs_sejour.tpl");

?>