<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

function updateFieldMed($table, $mb_field, $bcb_field){
  $ds_std = CSQLDataSource::get("std");
  $ds_bcb = CBcbObject::getDataSource();

  $nb_modif = 0;
  $cips = array();
  
  $query = "SELECT DISTINCT code_cip 
	        FROM $table
					WHERE $mb_field = '';";
  $codes_cip = $ds_std->loadColumn($query);

	foreach($codes_cip as $code_cip){
	  $query = "SELECT $bcb_field FROM `IDENT_PRODUITS`
			      WHERE `IDENT_PRODUITS`.`CODE_CIP` = '$code_cip';";
	  $cips[$code_cip] = $ds_bcb->loadResult($query);
	}
	
	foreach($cips as $code_cip => $_code){
	  $query = "UPDATE `$table`
	          SET `$mb_field` = '$_code'
			      WHERE `code_cip` = '$code_cip'
	          AND `$mb_field` = '';";
	  $ds_std->exec($query);
	  $nb_modif += $ds_std->affectedRows();
	}
	CAppUI::stepAjax("$nb_modif codes $mb_field mis à jour dans la table $table", UI_MSG_OK);
}

// Remplissage des codes UCD et CIS
updateFieldMed('prescription_line_medicament','code_ucd', 'CODE_UCD');
updateFieldMed('prescription_line_medicament','code_cis', 'CODECIS');
updateFieldMed('prescription_line_mix_item','code_ucd', 'CODE_UCD');
updateFieldMed('prescription_line_mix_item','code_cis', 'CODECIS');

?>