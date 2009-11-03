<?php /* $Id: vw_sortie_rpu.php 6854 2009-09-03 16:16:08Z lryo $ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: 6854 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can, $m;

$can->needsAdmin();

$repair = CValue::post('repair', 0);

$sejour_no_prat = array();
$sejour = new CSejour();

$ljoin = array();
$ljoin["users"] = "`users`.`user_id` = `sejour`.`praticien_id`";
  
$where = array();
// Type non Praticien, Anesthésiste, Medecin
$where["users.user_type"] = " != '13' AND users.user_type != '3' AND users.user_type != '4'";

$order = "sejour.sortie_reelle DESC";

if ($repair) {
  $sejours = $sejour->loadList($where, $order, null, null, $ljoin);
  foreach ($sejours as $_sejour) {
    $_sejour->loadRefPraticien();
    $_sejour->loadNumDossier();
    $_sejour->loadRefsConsultations();
    $consult_atu = $_sejour->_ref_consult_atu;
    $consult_atu->loadRefPlageConsult();
    if ($consult_atu->_ref_chir->_id) {
      $_sejour->praticien_id = $consult_atu->_ref_chir->_id;
      $_sejour->store();
    }
  }
}

$sejours = $sejour->loadList($where, $order, null, null, $ljoin);
foreach ($sejours as $_sejour) {
  $_sejour->loadNumDossier();
  $_sejour->loadRefPraticien();
  $_sejour->loadRefsConsultations();
  $_sejour->_ref_consult_atu->loadRefPlageConsult();
  $sejour_no_prat[$_sejour->_ref_praticien->_id][] = $_sejour;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour_no_prat", $sejour_no_prat);
$smarty->assign("sejours"       , $sejours);

$smarty->display("vw_resp_no_prat.tpl");
?>