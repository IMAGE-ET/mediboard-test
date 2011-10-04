<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$user = CUser::get();

$code_cip    = CValue::get("code_cip");
$line_id     = CValue::get("line_id");
$mode_pharma = CValue::get("mode_pharma"); 
$mode_protocole = CValue::get("mode_protocole");

// Chargement de la ligne à substituer
$line = new CPrescriptionLineMedicament();
$line->load($line_id);

// Chargement des variantes possibles de la ligne
$line->loadRefsVariantes();

// Creation de la nouvelle ligne
$line->_id = "";
$line->code_cip = $code_cip;
$line->code_ucd = "";
$line->creator_id = $user->_id;
$line->accord_praticien = "";
$line->substitute_for_id = $line_id;
$line->substituted = 0;
$line->voie = "";
$msg = $line->store();

// Réaffectation des lignes de variantes à l'equivalent
foreach($line->_ref_variantes as $subst_lines){
  foreach($subst_lines as $_subst_line){
    $_subst_line->variante_for_id = $line->_id;
    $msg = $_subst_line->store();
    CAppUI::displayMsg($msg, "$_subst_line->_class-msg-store");
  }
}

CAppUI::displayMsg($msg, "CPrescriptionLineMedicament-msg-create");
    
// Sauvegarde de l'ancienne ligne
$old_line = new CPrescriptionLineMedicament();
$old_line->load($line_id);
$old_line->substituted = "1";
//$old_line->date_arret = mbDate();
//$old_line->time_arret = mbTime();
$msg = $old_line->store();
CAppUI::displayMsg($msg, "CPrescriptionLineMedicament-msg-store");

// Le passage de la ligne au reload permet de realiser le testPharma (pre-cochage de la case "Accord du praticien")
echo "<script type='text/javascript'>Prescription.reloadLine('$line->_guid','$mode_protocole', '$mode_pharma')</script>";
echo CAppUI::getMsg();
CApp::rip();
?>