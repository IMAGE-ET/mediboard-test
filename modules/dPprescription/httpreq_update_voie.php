<?php

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can;

$can->needsAdmin();

$ds_std = CSQLDataSource::get("std");
$ds_bcb = CBcbObject::getDataSource();
        
// Recuperation des tous les codes cip
$sql = "SELECT DISTINCT code_cip 
        FROM prescription_line_medicament;";
$codes_cip = $ds_std->loadColumn($sql);

// Parcours des cip et stockage de la premiere voie trouvée
foreach($codes_cip as $code_cip){
  $sql = "SELECT IDENT_VOIES.LIBELLEVOIE FROM `IDENT_VOIES`
		 LEFT JOIN `IDENT_PRODUITS_VOIES` ON `IDENT_PRODUITS_VOIES`.`CODEVOIE` = `IDENT_VOIES`.`CODEVOIE`
		 WHERE `IDENT_PRODUITS_VOIES`.`CODECIP` = '$code_cip';";
  $voie = $ds_bcb->loadHash($sql);
  $codeCip_voie[$code_cip] = $voie["LIBELLEVOIE"];
}
$nb_lines = 0;
foreach($codeCip_voie as $code_cip => $libelle_voie){
  $sql = "UPDATE `prescription_line_medicament`
          SET `voie` = '$libelle_voie'
		      WHERE `code_cip` = '$code_cip'
          AND `voie` IS NULL;";
  $ds_std->exec($sql);
  $nb_lines += $ds_std->affectedRows();
}
 
$AppUI->stepAjax("$nb_lines lignes modifiées", UI_MSG_OK);

?>



