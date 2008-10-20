<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */


global $AppUI;

$prescription_line_medicament_id = mbGetValueFromPost("prescription_line_medicament_id");

// Chargement de la ligne à rendre active
$line = new CPrescriptionLineMedicament();
$line->load($prescription_line_medicament_id);
$line->substitution_active = 1;

$msg = $line->store();
$AppUI->displayMsg($msg, "CPrescriptionLineMedicament-msg-modify");
// Desactivation des autres lignes

// Si la ligne est deja une ligne de substitution
if($line->substitute_for){
  // On desactive la ligne originale
  $_line = new CPrescriptionLineMedicament();
  $_line->load($line->substitute_for);
  if($_line->substitution_active == 1){
    $_line->substitution_active = 0;  
    $msg = $_line->store();
    $AppUI->displayMsg($msg, "CPrescriptionLineMedicament-msg-modify");
  }

  $_line->loadRefsSubstitutionLines();
  // On desactive les autres lignes de substitution
  foreach($_line->_ref_substitution_lines as &$_line_sub){
    if($_line_sub->substitution_active && $_line_sub->_id != $line->_id && $_line_sub->substitution_active == 1){
      $_line_sub->substitution_active = 0;
      $msg = $_line_sub->store();
      $AppUI->displayMsg($msg, "CPrescriptionLineMedicament-msg-modify");
    }
  }
}

// Si la ligne est l'originale, on desactive les lignes de substitution
if(!$line->substitute_for){
  $line->loadRefsSubstitutionLines();
  foreach($line->_ref_substitution_lines as &$_line_sub){
    if($_line_sub->substitution_active){
      $_line_sub->substitution_active = 0;
      $msg = $_line_sub->store();
      $AppUI->displayMsg($msg, "CPrescriptionLineMedicament-msg-modify");
    }
  }
}

echo $AppUI->getMsg();
CApp::rip();
?>