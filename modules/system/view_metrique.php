<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

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
  $statusTable = db_loadList($sql);
  if($statusTable){
    $result[$keyListTables]["descr"] = $currListTables;
    $result[$keyListTables]["nombre"] = $statusTable[0]["Rows"];
  }
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("result" , $result);

$smarty->display("view_metrique.tpl");

?>