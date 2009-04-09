<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $g;

$praticien_id = mbGetValueFromGetOrSession("praticien_id");
$anesth_id = mbGetValueFromGetOrSession("anesth_id");
$all_prot = mbGetValueFromGetOrSession("all_prot");

$mediuser =& $AppUI->_ref_user;

$protocoles = array();
$praticiens = array();
$anesths = array();
$is_anesth = $mediuser->isFromType(array("Anesthésiste"));
$is_chir   = $mediuser->isFromType(array("Chirurgien"));
$is_admin  = $mediuser->isFromType(array("Administrator"));
$praticien = new CMediusers();
$chir = new CMediusers();
$anesth = new CMediusers();
$protocoles_list = array('chir' => array(), 'anesth' => array());

// Chargement des protocoles de l'etablissement
if($is_anesth || $is_admin){        
  if($is_admin){
    $anesths = $mediuser->loadAnesthesistes();
    if($anesth_id){
      $anesth = new CMediusers();
      $anesth->load($anesth_id);    
    }
  } else {
    $anesth =& $mediuser;  
  }
  
	// Chargement de la liste des praticiens
  $praticiens   = $mediuser->loadChirurgiens();
  
  // Chargement des protocoles d'admissions du chirurgien selectionné
  if($praticien_id){
    $praticien = new CMediusers();
    $praticien->load($praticien_id);
    $praticien->loadProtocoles();
    foreach ($praticien->_ref_protocoles as &$_protocole_anesth) {
      if($is_anesth && $_protocole_anesth->protocole_prescription_anesth_id && !$all_prot){
        continue;
      }
      if($is_admin && $_protocole_anesth->protocole_prescription_anesth_id && $_protocole_anesth->protocole_prescription_chir_id && !$all_prot){
        continue;
      }
	    $_protocole_anesth->loadRefsFwd();
	    $protocoles[$_protocole_anesth->_id] = $_protocole_anesth;    	
  	}
  	$chir =& $praticien;
  }    
  
}

// Chargement des protcoles du chir
if($is_chir){
  $mediuser->loadProtocoles();
  foreach ($mediuser->_ref_protocoles as &$_protocole_chir) {
    if($_protocole_chir->protocole_prescription_chir_id && !$all_prot){
      continue;
    }  
	  $_protocole_chir->loadRefsFwd();
	  $protocoles[$_protocole_chir->_id] = $_protocole_chir;    	
  }
  $chir =& $mediuser;
}

if($chir->_id){
	$protocoles_list["chir"] = CPrescription::loadAllProtocolesFor($chir->_id, null, null, 'CSejour');
}

if($anesth->_id){
  $protocoles_list["anesth"] = CPrescription::loadAllProtocolesFor($anesth->_id, null, null, 'CSejour');
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("protocoles_list", $protocoles_list);

$smarty->assign("praticiens",   $praticiens);
$smarty->assign("anesths",      $anesths);

$smarty->assign("protocoles",   $protocoles);

$smarty->assign("is_anesth",    $is_anesth);
$smarty->assign("is_chir",      $is_chir);
$smarty->assign("is_admin",     $is_admin);

$smarty->assign("praticien_id", $praticien_id);
$smarty->assign("anesth_id",    $anesth_id);

$smarty->assign("all_prot",     $all_prot);
$smarty->display("vw_edit_liaison_admission.tpl");

?>