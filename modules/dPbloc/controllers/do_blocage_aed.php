<?php

/**
 * dPbloc
 *
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

// Suppression des plages vides concernées
if ($_POST["del"] == 0) {
  $salle_id = CValue::post("salle_id");
  $plage = new CPlageOp;
  
  $where = array();

  $where["salle_id"] = "= '$salle_id'";
  $where["date"]     = "BETWEEN '".$_POST['deb'] . "' AND '".$_POST['fin'] . "'";
  
  $plages = $plage->loadList($where);
  
  foreach ($plages as $_plage) {
    if ($_plage->countBackRefs("operations") == 0) {
      if ($msg = $_plage->delete()) {
        CAppUI::setMsg($msg);
      }
      else {
        CAppUI::setMsg(CAppUI::tr("CPlageOp-msg-delete"));
      }
    }
  }
}

$do = new CDoObjectAddEdit("CBlocage");
$do->doIt();
