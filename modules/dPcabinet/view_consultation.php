<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Yohann Poiron
*/

CCanDo::checkRead();

$consultation_id = CValue::get("consultation_id");

$consultation = new CConsultation;
$consultation->load($consultation_id);
$consultation->loadRefsFwd();

$prat = $consultation->_ref_plageconsult->_ref_chir;
$prat->loadRefs();

$patient = $consultation->_ref_patient;
$patient->loadRefs();

$today = CMbDT::date();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consultation", $consultation	);
$smarty->assign("patient"     , $patient      );
$smarty->assign("prat"        , $prat         );
$smarty->assign("today"       , $today        );

$smarty->display("view_consultation.tpl");

?>