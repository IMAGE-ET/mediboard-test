<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$operation_id = mbGetValueFromGetOrSession("operation_id");
$selOp = new COperation();
$selOp->load($operation_id);

$selOp->loadRefsConsultAnesth();
$selOp->_ref_consult_anesth->loadRefConsultation();

	// Affichage des données
	$listChamps = array(
	                1=>array("hb","ht","ht_final","plaquettes"),
	                2=>array("creatinine","_clairance","na","k"),
	                3=>array("tp","tca","tsivy","ecbu")
	                );
	$cAnesth =& $selOp->_ref_consult_anesth;
	foreach($listChamps as $keyCol=>$aColonne){
		foreach($aColonne as $keyChamp=>$champ){
		  $verifchamp = true;
	    if($champ=="tca"){
		    $champ2 = $cAnesth->tca_temoin;
		  }else{
		    $champ2 = false;
	      if(($champ=="ecbu" && $cAnesth->ecbu=="?") || ($champ=="tsivy" && $cAnesth->tsivy=="00:00:00")){
	        $verifchamp = false;
	      }
		  }
	    $champ_exist = $champ2 || ($verifchamp && $cAnesth->$champ);
	    if(!$champ_exist){
	      unset($listChamps[$keyCol][$keyChamp]);
	    }
		}
	}

	$selOp->_ref_consult_anesth->_ref_consultation->loadView();
	$selOp->_ref_consult_anesth->_ref_consultation->loadRefsBack();

	

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listChamps", $listChamps);
$smarty->assign("selOp", $selOp);

$smarty->display("inc_vw_info_anesth.tpl");

?>