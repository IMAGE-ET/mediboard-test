<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


global $AppUI;

$code_cip    = mbGetValueFromGet("code_cip");
$line_id     = mbGetValueFromGet("line_id");
$mode_pharma = mbGetValueFromGet("mode_pharma"); 
$mode_protocole = mbGetValueFromGet("mode_protocole");

// Chargement de la ligne à substituer
$line = new CPrescriptionLineMedicament();
$line->load($line_id);

// Chargement des substitutions possibles de la ligne
$line->loadRefsSubstitutionLines();

// Creation de la nouvelle ligne
$line->_id = "";
$line->code_cip = $code_cip;
$line->creator_id = $AppUI->user_id;
$line->accord_praticien = "";
$line->debut = mbDate();
$line->time_debut = mbTime();
$msg = $line->store();

// Réaffectation des lignes de substitutions à l'equivalent
foreach($line->_ref_substitution_lines as $subst_lines){
  foreach($subst_lines as $_subst_line){
    $_subst_line->substitute_for_id = $line->_id;
    $msg = $_subst_line->store();
    $AppUI->displayMsg($msg, "$_subst_line->_class_name-msg-store");
  }
}

$AppUI->displayMsg($msg, "CPrescriptionLineMedicament-msg-create");
    
// Sauvegarde de l'ancienne ligne
$old_line = new CPrescriptionLineMedicament();
$old_line->load($line_id);
$old_line->substitution_line_id = $line->_id;
$old_line->date_arret = mbDate();
$old_line->time_arret = mbTime();
$msg = $old_line->store();
$AppUI->displayMsg($msg, "CPrescriptionLineMedicament-msg-store");

// Le passage de la ligne au reload permet de realiser le testPharma (pre-cochage de la case "Accord du praticien")
echo "<script type='text/javascript'>Prescription.reload($line->prescription_id, '', 'medicament', '$mode_protocole', '$mode_pharma','$line->_id')</script>";
echo $AppUI->getMsg();
CApp::rip();
?>