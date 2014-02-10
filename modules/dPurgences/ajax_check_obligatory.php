<?php 

/**
 * $Id$
 *  
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */


$rpu_id = CValue::get("rpu_id");

$rpu = new CRPU();
$rpu->load($rpu_id);
$consult = $rpu->loadRefConsult();
$consult->loadRefsActes();
if ($rpu->mutation_sejour_id) {
  $rpu->loadRefSejourMutation()->loadRefsActes();
}

$group = CGroups::loadCurrent();

$cotation  = CAppUI::conf("dPurgences Display check_cotation" , $group);
$gemsa     = CAppUI::conf("dPurgences Display check_gemsa"    , $group);
$ccmu      = CAppUI::conf("dPurgences Display check_ccmu"     , $group);
$dp        = CAppUI::conf("dPurgences Display check_dp"       , $group);

$value = array(true);

if ($cotation > 1) {
  if ((!$rpu->_ref_consult->_ref_actes && !$rpu->mutation_sejour_id) ||
      ($rpu->mutation_sejour_id && !$rpu->_ref_sejour_mutation->_count_actes)
  ) {
    array_push($value, "Cotation");
  }
}

if ($gemsa > 1) {
  if (!$rpu->gemsa) {
    array_push($value, "CRPU-gemsa");
  }
}

if ($ccmu > 1) {
  if (!$rpu->ccmu) {
    array_push($value, "CRPU-ccmu");
  }
}

if ($dp > 1) {
  if (!$rpu->_DP) {
    array_push($value, "CRPU-_DP");
  }
}

CApp::json($value);