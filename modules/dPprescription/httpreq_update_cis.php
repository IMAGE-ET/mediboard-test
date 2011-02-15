<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

set_time_limit(300);

CCanDo::checkAdmin();

function updateFieldMed($table){
  $ds_std = CSQLDataSource::get("std");
  $ds_bcb = CBcbObject::getDataSource();

  $nb_modif = 0;
  $cips = array();
  
  $query = "SELECT DISTINCT code_cip 
          FROM $table;";
  $codes_cip = $ds_std->loadColumn($query);

  foreach($codes_cip as $code_cip){
    $query = "SELECT CODECIS FROM `IDENT_PRODUITS`
            WHERE `IDENT_PRODUITS`.`CODE_CIP` = '$code_cip';";
    $cips[$code_cip] = $ds_bcb->loadResult($query);
  }
  
  foreach($cips as $code_cip => $_code){
    $query = "UPDATE `$table`
            SET `code_cis` = '$_code'
            WHERE `code_cip` = '$code_cip';";
    $ds_std->exec($query);
    $nb_modif += $ds_std->affectedRows();
  }
  CAppUI::stepAjax("$nb_modif codes CIS mis à jour dans la table $table", UI_MSG_OK);
}

// Remplissage des CIS
updateFieldMed('prescription_line_medicament');
updateFieldMed('prescription_line_mix_item');

?>