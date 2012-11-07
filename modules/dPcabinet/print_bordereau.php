<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

$date = mbDate();

// Dates selectionnees
$date_min = CValue::getOrSession("_date_min", mbDate());
$date_max = CValue::getOrSession("_date_max", mbDate());

// Id du praticien selectionné
$prat = CValue::get("chir");

// Chargement du praticien
$praticien = new CMediusers();
$praticien->load($prat);
$praticien->loadRefBanque();
if (!$praticien->_id) {
  CAppUI::stepMessage(UI_MSG_WARNING, "CMediusers-warning-undefined");
  return;
}

// Extraction des elements qui composent le numero de compte
$compte_banque  = substr($praticien->compte,  0,  5);
$compte_guichet = substr($praticien->compte,  5,  5);
$compte_numero  = substr($praticien->compte, 10, 11);
$compte_cle     = substr($praticien->compte, 21,  2);

// Nombre de cheques remis
$nbRemise = 0;

// Montant total des cheques
$montantTotal = 0;

$where = array();
$ljoin = array();

// Chargement des règlements via les consultations
$ljoin["consultation"] = "reglement.object_id = consultation.consultation_id";
$ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";
$where["object_class"] = " = 'CConsultation'";
$where['plageconsult.chir_id'] = "= '$praticien->_id'";
$where['reglement.mode'] = "= 'cheque' ";
$where['reglement.date'] = "BETWEEN '$date_min' AND '$date_max 23:59:59' ";
$order = "reglement.date ASC";

$reglement = new CReglement();
$reglements_consult = $reglement->loadList($where, $order, null, null, $ljoin);

// Chargement des règlements via les factures
$ljoin["consultation"] = "reglement.object_id = consultation.factureconsult_id";
$where["object_class"] = " = 'CFactureConsult'";

$reglement = new CReglement();
$reglements_facture = $reglement->loadList($where, "reglement.date, plageconsult.chir_id", null, null, $ljoin);

$reglements = array_merge($reglements_consult, $reglements_facture);

// Chargements des consultations
$montantTotal = 0.0;
foreach ($reglements as $_reglement) {
  $_reglement->loadTargetObject()->loadRefPatient();
  $_reglement->loadRefBanque();
  $montantTotal += $_reglement->montant;
}
$nbRemise = count($reglements);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("praticien"      , $praticien     );
$smarty->assign("reglements"     , $reglements    );
$smarty->assign("date"           , $date          );
$smarty->assign("compte_banque"  , $compte_banque );
$smarty->assign("compte_guichet" , $compte_guichet);
$smarty->assign("compte_numero"  , $compte_numero );
$smarty->assign("compte_cle"     , $compte_cle    );
$smarty->assign("montantTotal"   , $montantTotal  );
$smarty->assign("nbRemise"       , $nbRemise      );

$smarty->display("print_bordereau.tpl");

?>