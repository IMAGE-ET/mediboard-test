<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Liste des étudiants
$etudiant = new CMediusers;
$etudiants = $etudiant->loadListFromType(array("Dentiste"), PERM_READ);

// Calcul de :
// - ICR réalisé
// - ICR moyen
// - ICR max déjà réalisé

$icr_base = CAppUI::conf("aphmOdonto icr_base");
$ds = CSQLDataSource::get("std");

foreach ($etudiants as $_etudiant) {
  $_etudiant->loadRefFunction();
  $sql = "SELECT sum(ICR) as ICR_realise, count(*) AS nombre_actes, AVG(ICR) as ICR_moyen, max(ICR) as ICR_max
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

$smarty->display("inc_choose_etudiant.tpl");

?>