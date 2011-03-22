<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$line_guid = CValue::get("line_guid");

$line = CMbObject::loadFromGuid($line_guid);

$lines = array();

// Chargement de l'historique de la ligne selectionnée
$lines[$line->_guid] = $line->loadRefsParents();;

// Chargement des lignes similaires (meme element ou meme medicament)
$equivalents = array();
if($line instanceof CPrescriptionLineElement){
	$_line = new CPrescriptionLineElement();
	$where = array();
  $where["prescription_id"] = " = '$line->prescription_id'";
  $where["element_prescription_id"] = " = '$line->element_prescription_id'";
  $where["prescription_line_element_id"] = " != '$line->_id'";
	$equivalents =  $_line->loadList($where);
}

if($line instanceof CPrescriptionLineMedicament){
  $_line = new CPrescriptionLineMedicament();
	$where = array();
	$where["prescription_id"] = " = '$line->prescription_id'";
	$where["code_cip"] = " = '$line->code_cip'";
	$where["child_id"] = " IS NULL";
	$where["prescription_line_medicament_id"] = " != '$line->_id'";
  $equivalents = $_line->loadList($where);
}

// Chargement des parents des lignes semblables
foreach($equivalents as $_equivalent){
	$lines[$_equivalent->_guid] = $_equivalent->loadRefsParents();
}

foreach($lines as $_lines){
	foreach($_lines as $_line){
	  if($_line instanceof CPrescriptionLineMix){
	    $_line->loadRefsLines();
	  } else {
	    $_line->loadRefsPrises();
	  }
	}
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("line", $line);
$smarty->assign("lines", $lines);
$smarty->display("vw_line_history.tpl");

?>