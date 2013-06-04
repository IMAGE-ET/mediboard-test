<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

$devenir_dentaire_id = CValue::get("devenir_dentaire_id");

// Liste des étudiants
$etudiant = new CMediusers;
$etudiants = $etudiant->loadListFromType(array("Dentiste"), PERM_READ);

$devenir_dentaire = new CDevenirDentaire;
$devenir_dentaire->load($devenir_dentaire_id);

$actes_dentaires = $devenir_dentaire->loadRefsActesDentaires();

foreach ($actes_dentaires as $_acte_dentaire) {
  $devenir_dentaire->_total_ICR += $_acte_dentaire->ICR;
  $devenir_dentaire->_max_ICR = max($devenir_dentaire->_max_ICR, $_acte_dentaire->ICR);
}

// Calcul de :
// - ICR réalisé
// - ICR moyen
// - ICR max déjà réalisé

$icr_base = CAppUI::conf("aphmOdonto icr_base");
$ds = CSQLDataSource::get("std");

$etudiants_calcul_icr = array();
foreach ($etudiants as $_etudiant) {
  $_etudiant->loadRefFunction();
  $sql = "SELECT sum(ICR) as ICR_realise, count(*) AS nombre_actes, ROUND(AVG(ICR),0) as ICR_moyen, max(ICR) as ICR_max
    FROM devenir_dentaire
    LEFT JOIN acte_dentaire ON devenir_dentaire.devenir_dentaire_id = acte_dentaire.devenir_dentaire_id
    WHERE devenir_dentaire.etudiant_id = '$_etudiant->_id'
    AND acte_dentaire.consult_id IS NOT NULL";
  
  $etudiants_calcul_icr[$_etudiant->_id] = reset($ds->loadList($sql));
  
  $etudiants_calcul_icr[$_etudiant->_id]["ICR_pending"] = $icr_base - $etudiants_calcul_icr[$_etudiant->_id]["ICR_realise"];
}

$smarty = new CSmartyDP;

$smarty->assign("etudiants", $etudiants);
$smarty->assign("etudiants_calcul_icr", $etudiants_calcul_icr);
$smarty->assign("devenir_dentaire", $devenir_dentaire);
$smarty->display("inc_choose_etudiant.tpl");
