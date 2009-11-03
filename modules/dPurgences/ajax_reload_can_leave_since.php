<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;

$can->needsRead();

$rpus = CValue::get("rpus", array());

$listRpu = array();

foreach($rpus as $_rpu_id) {
	$rpu = new CRPU();
	$rpu->load($_rpu_id);
  $rpu->loadRefSejour();
  
  $_sortie_autorisee = $rpu->sortie_autorisee ? CAppUI::tr('CRPU-sortie_assuree.1') : CAppUI::tr('CRPU-sortie_assuree.0');
  
  if ($rpu->_ref_sejour->sortie_reelle) {
  	$listRpu[$_rpu_id]["value"] = "";
  } else if ($rpu->_can_leave == -1) {
		$listRpu[$_rpu_id]["value"] = CAppUI::tr("CConsultation")." ".CAppUI::tr("CConsultation.chrono.48")."<br />".$_sortie_autorisee;
	} else {
		if (!$rpu->sortie_autorisee) {
			$listRpu[$_rpu_id]["value"] = CAppUI::tr("CConsultation")." ".CAppUI::tr("CConsultation.chrono.64")."<br />".$_sortie_autorisee;
		} else {
		  if ($rpu->_can_leave_since) {
        $listRpu[$_rpu_id]["value"] = CAppUI::tr("CRPU-_can_leave_since")." ";
      }
		  if ($rpu->_can_leave_about) {
        $listRpu[$_rpu_id]["value"] = CAppUI::tr("CRPU-_can_leave_about")." ";
      }
			
			$listRpu[$_rpu_id]["value"] .= "<span title='".$rpu->_ref_sejour->sortie_prevue."'>".mbTransformTime($rpu->_can_leave, null, "%Hh%M"). "</span><br />".$_sortie_autorisee;
		}
	}
	$listRpu[$_rpu_id]["value"] = utf8_encode($listRpu[$_rpu_id]["value"]);
	
	
	$_class_sortie_autorise = $rpu->sortie_autorisee ? "" : "arretee"; 
	if ($rpu->_ref_sejour->sortie_reelle) {
		$listRpu[$_rpu_id]["alert"] = "";
	} else if ($rpu->_can_leave_warning) {
		$listRpu[$_rpu_id]["alert"] = "$_class_sortie_autorise warning";
	} else if ($rpu->_can_leave_error) {
		$listRpu[$_rpu_id]["alert"] = "$_class_sortie_autorise error";
	} else {
		$listRpu[$_rpu_id]["alert"] = "$_class_sortie_autorise ok";
	}
}

echo json_encode($listRpu);
?>