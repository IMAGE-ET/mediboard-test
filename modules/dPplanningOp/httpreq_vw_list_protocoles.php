<?php /* $Id: vw_protocoles.php 7210 2009-11-03 12:18:57Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: 7210 $
* @author Thomas Despoix
*/

global $AppUI, $can, $m, $dialog;

if($dialog) {
  $can->needsRead();
} else {
  $can->needsEdit();
}

// L'utilisateur est-il chirurgien?
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);

$chir_id   = $mediuser->isPraticien() ? $mediuser->user_id : null;
$chir_id   = CValue::getOrSession("chir_id", $chir_id);
$code_ccam = CValue::getOrSession("code_ccam");
$type_protocole = CValue::get("type_protocole"); 
$page = intval(CValue::get('page', 0));

$protocole_sejour = array ();
$protocole_interv = array ();
$protocole        = new CProtocole; 

$where = array();
$limit = "$page, 20";
	  
if ($chir_id) {
	$where["chir_id"] = "= '$chir_id'";
}
if ($code_ccam) {
	$where["codes_ccam"] = "= '$code_ccam'";
}

$order = "libelle_sejour, libelle, codes_ccam";

if ($type_protocole == 'interv'){
  $where["for_sejour"] = "= '0'";
	$protocole_interv = $protocole->loadList($where,$order,$limit);
	$nb["interv"]= $protocole->countList($where);

  $where["for_sejour"] = "= '1'";
	$protocole_sejour = $protocole->loadList($where,$order,"0,20");
	$nb["sejour"]= $protocole->countList($where);
}
else if($type_protocole == 'sejour')  {
	$where["for_sejour"] = "= '1'";
	$protocole_sejour = $protocole->loadList($where,$order,$limit);
	$nb["sejour"]= $protocole->countList($where);
	
	$where["for_sejour"] = "= '0'";
	$protocole_interv = $protocole->loadList($where,$order,"0,20");
	 $nb["interv"]= $protocole->countList($where);
}
else {
	$where["for_sejour"] = "= '0'";
	$protocole_interv = $protocole->loadList($where,$order,"$limit");
	$nb["interv"]= $protocole->countList($where);
	
  $where["for_sejour"] = "= '1'";
  $protocole_sejour = $protocole->loadList($where,$order,"$limit");
  $nb["sejour"]= $protocole->countList($where);
}

$protocoles = array(
  'sejour' => array(),
  'interv' => array(),
);

$protocoles['interv'] = $protocole_interv;
$protocoles['sejour'] = $protocole_sejour;

// Protocole seectionn�
$protSel = new CProtocole;
if($protocole_id = CValue::getOrSession("protocole_id")) {
  $protSel->load($protocole_id);
  $protSel->loadRefs();
}


// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("protocoles", $protocoles);
$smarty->assign("nb"        , $nb        );
$smarty->assign("page"      , $page      );
$smarty->assign("protSel"   , $protSel   );
$smarty->assign("chir_id"   , $chir_id   );
$smarty->assign("code_ccam" , $code_ccam );

$smarty->display("inc_list_protocoles.tpl");

?>