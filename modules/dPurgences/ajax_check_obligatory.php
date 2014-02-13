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

$cotation     = CAppUI::conf("dPurgences Display check_cotation" , $group);
$gemsa        = CAppUI::conf("dPurgences Display check_gemsa"    , $group);
$ccmu         = CAppUI::conf("dPurgences Display check_ccmu"     , $group);
$dp           = CAppUI::conf("dPurgences Display check_dp"       , $group);
$display_sfmu = CAppUI::conf("dPurgences display_motif_sfmu");
$sfmu         = CAppUI::conf("dPurgences CRPU gestion_motif_sfmu" , $group);

$value = array();

if ($cotation > 1) {
  if ((!$rpu->_ref_consult->_ref_actes && !$rpu->mutation_sejour_id) ||
      ($rpu->mutation_sejour_id && !$rpu->_ref_sejour_mutation->_count_actes)
  ) {
    $value[] = "Cotation";
  }
}

if ($gemsa > 1) {
  if (!$rpu->gemsa) {
    $value[] = "CRPU-gemsa";
  }
}

if ($ccmu > 1) {
  if (!$rpu->ccmu) {
    $value[] = "CRPU-ccmu";
  }
}

if ($dp > 1) {
  if (!$rpu->_DP) {
    $value[] = "CRPU-_DP";
  }
}

if ($display_sfmu && $sfmu > 1) {
  if (!$rpu->motif_sfmu) {
    $value[] = "CRPU-motif_sfmu";
  }
}

CApp::json($value);