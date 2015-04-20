<?php 

/**
 * $Id$
 *  
 * @category Maternité
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$date = CView::request("date", "date default|".CMbDT::date());

CView::checkin();

$date_max = CMbDT::date("+2 month", $date);
$group = CGroups::loadCurrent();

$grossesse = new CGrossesse();

$where = array();

$where["terme_prevu"] = "BETWEEN '$date' AND '$date_max'";
$where["group_id"] = "= '$group->_id'";

$grossesses = $grossesse->loadList($where);

$sejours = CStoredObject::massLoadBackRefs($grossesses, "sejours", "entree_prevue DESC");
CStoredObject::massLoadBackRefs($sejours, "operations", "date ASC");

$fiches_anesth = array();

$params = array(
  "dossier_anesth_id" => "",
  "operation_id"      => "",
  "offline"           => 1,
  "print"             => 1,
  "pdf"               => 0
);

/** @var CGrossesse $_grossesse */
foreach ($grossesses as $_grossesse) {
  foreach ($_grossesse->loadRefsSejours() as $_sejour) {
    // Le séjour lié à l'intervention doit être de type obstétrique
    if ($_sejour->type_pec == "O") {
      $_sejour->loadRefsOperations();

      /** @var COperation $op */
      $op = $_sejour->_ref_last_operation;
      if ($op->_id) {
        $dossier_anesth = $op->loadRefsConsultAnesth();

        if ($dossier_anesth->_id) {
          $params["dossier_anesth_id"] = $op->_ref_consult_anesth->_id;
          $params["operation_id"]      = $op->_id;
          $fiches_anesth[$op->_id]     = CApp::fetch("dPcabinet", "print_fiche", $params);
        }
      }
    }
    break;
  }
}

$smarty = new CSmartyDP();

$smarty->assign("fiches_anesth", $fiches_anesth);

$smarty->display("offline_print_fiches.tpl");