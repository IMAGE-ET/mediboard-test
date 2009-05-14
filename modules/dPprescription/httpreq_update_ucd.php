<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

function updateFieldMed($table, $mb_field, $bcb_field){
  global $AppUI;
  
  $ds_std = CSQLDataSource::get("std");
  $ds_bcb = CBcbObject::getDataSource();

  $nb_modif = 0;
  $cips = array();
  
  $sql = "SELECT DISTINCT code_cip 
	        FROM $table
					WHERE $mb_field = '';";
  $codes_cip = $ds_std->loadColumn($sql);

	foreach($codes_cip as $code_cip){
	  $sql = "SELECT $bcb_field FROM `IDENT_PRODUITS`
			      WHERE `IDENT_PRODUITS`.`CODE_CIP` = '$code_cip';";
	  $cips[$code_cip] = $ds_bcb->loadResult($sql);
	}
	
	foreach($cips as $code_cip => $_code){
	  $sql = "UPDATE `$table`
	          SET `$mb_field` = '$_code'
			      WHERE `code_cip` = '$code_cip'
	          AND `$mb_field` = '';";
	  $ds_std->exec($sql);
	  $nb_modif += $ds_std->affectedRows();
	}
	$AppUI->stepAjax("$nb_modif codes $mb_field mis à jour dans la table $table", UI_MSG_OK);
}

// Remplissage des codes UCD et CIS
updateFieldMed('prescription_line_medicament','code_ucd', 'CODE_UCD');
updateFieldMed('prescription_line_medicament','code_cis', 'CODECIS');
updateFieldMed('perfusion_line','code_ucd', 'CODE_UCD');
updateFieldMed('perfusion_line','code_cis', 'CODECIS');

?>