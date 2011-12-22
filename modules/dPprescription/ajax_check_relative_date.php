<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$pack_protocole_id = CValue::get("pack_protocole_id");
$pack_protocole = explode("-", $pack_protocole_id);

$pack_id = ($pack_protocole[0] === "pack") ? $pack_protocole[1] : "";
$protocole_id = ($pack_protocole[0] === "prot") ? $pack_protocole[1] : "";

$protocoles = array();

if($pack_id){
  $pack = new CPrescriptionProtocolePack();
  $pack->load($pack_id);
  $pack->loadRefsPackItems();
  
  foreach($pack->_ref_protocole_pack_items as $_pack_item){
    $_pack_item->loadRefPrescription();
    $_protocole =& $_pack_item->_ref_prescription;
    $protocoles[$_protocole->_id] = $_protocole;
	}
}
else {
	$protocole = new CPrescription();
	$protocole->load($protocole_id);
	$protocoles[$protocole->_id] = $protocole;
}          

$count = 0; 
foreach($protocoles as $_protocole){
  $where = array();
  $where[] = "jour_decalage = 'N' OR jour_decalage_fin = 'N'";
	$where["prescription_id"] = " = '$_protocole->_id'";
	  
  $prescription_line_medicament = new CPrescriptionLineMedicament();
  $count += $prescription_line_medicament->countList($where);
  
  $prescription_line_element = new CPrescriptionLineElement();
  $count += $prescription_line_element->countList($where);
  
  $prescription_line_mix = new CPrescriptionLineMix();
  $count += $prescription_line_mix->countList($where);
}

if($count > 0){
  echo 1;
} else {
  echo 0;
}

?>