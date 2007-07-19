<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

$can->needsRead();
$ds = CSQLDataSource::get("std");
$result = array();

// Nom des tables à récupérer
$listesTables = array( "users"           => "Utilisateurs",
                        "sallesbloc"      => "Salles d'opération",
                        "patients"        => "Dossiers patient",
                        "consultation"    => "Consultations",
                        "compte_rendu"    => "Comptes rendus",
                        "sejour"          => "Séjours",
                        "operations"      => "Opérations",
                        "files_mediboard" => "Fichiers joints");


foreach ($listesTables as $keyListTables => $currListTables){

  $sql="SHOW TABLE STATUS LIKE '$keyListTables'";
  $statusTable = $ds->loadList($sql);
  if($statusTable){
    $result[$keyListTables]["descr"] = $currListTables;
    $result[$keyListTables]["nombre"] = $statusTable[0]["Rows"];
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("result" , $result);

$smarty->display("view_metrique.tpl");

?>