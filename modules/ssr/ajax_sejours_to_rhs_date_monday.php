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

$date = CMbDT::date();
$rhs = new CRHS();
$join['sejour'] = "sejour.sejour_id = rhs.sejour_id";
$join['patients'] = "patients.patient_id = sejour.patient_id";
$where['sejour.annule'] = " = '0'";
$where['date_monday'] = " = '$rhs_date_monday'";
$order = "nom, prenom";
$sejours_rhs = $rhs->loadList($where, $order, null, null, $join);
foreach ($sejours_rhs as $_rhs) {
  $_rhs->loadRefsNotes();
  $sejour = $_rhs->loadRefSejour();
  $sejour->_ref_patient->loadIPP();
}

$where['rhs.facture'] = " = '0'";
$count_sej_rhs_no_charge = $rhs->countList($where, null, $join);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejours_rhs"            , $sejours_rhs);
$smarty->assign("count_sej_rhs_no_charge", $count_sej_rhs_no_charge);
$smarty->assign("rhs_date_monday"        , $rhs_date_monday);
$smarty->assign("read_only"              , true);

$smarty->display("inc_vw_rhs_sejour.tpl");

?>