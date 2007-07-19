<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$can->needsRead();
$ds = CSQLDataSource::get("std");
set_time_limit( 1800 );

$sql = "SELECT * FROM import_medecins";
$listImport = $ds->loadlist($sql);

$new = 0;
$link = 0;
$total = 0;

foreach($listImport as $key => $value) {
  $tmpNom = addslashes(trim($value["nom"]));
  if($tmpNom == '') $tmpNom = "-";
  $tmpPrenom = addslashes(trim($value["prenom"]));
  if($tmpPrenom == '') $tmpPrenom = "-";
  $sql = "SELECT * FROM medecin" .
  		"\nWHERE medecin.nom = '$tmpNom'" .
  		"\nAND medecin.prenom = '$tmpPrenom'" .
  		"\nAND medecin.cp = '".$value["cp"]."'";
  $match = $ds->loadlist($sql);
  //echo "$total : Cas de ".$value["nom"]." ".$value["prenom"]." dans le ".$value["cp"]." :<br>";
  if(!count($match)) {
  	$med = new CMedecin;
  	// DB Table key
	$medecin_id = '';
    // DB Fields
    if(trim($value["nom"]) == '')
      $med->nom = "-";
    else
	  $med->nom = trim($value["nom"]);
	if(trim($value["prenom"]) == '')
	  $med->prenom = "-";
	else
	  $med->prenom = trim($value["prenom"]);
	$med->adresse = $value["adresse"];
	$med->ville = $value["ville"];
	$med->cp = $value["cp"];
	$med->tel = $value["tel1"];
	$med->fax = $value["tel2"];
	$med->email = $value["email"];
    $med->disciplines = $value["specialite"];
	$med->store();
	$sql = "UPDATE import_medecins" .
    		"\nSET mb_id = '".$med->medecin_id."'" .
    		"\nWHERE medecin_id = '".$value["medecin_id"]."'";
    $ds->exec($sql);
    $new++;
  } else {
    $sql = "UPDATE import_medecins" .
    		"\nSET mb_id = '".$match[0]["medecin_id"]."'" .
    		"\nWHERE medecin_id = '".$value["medecin_id"]."'";
    $ds->exec($sql);
    $link++;
  }
  $total++;
}

echo '<p>Opération terminée.</p>';
echo '<p>'.$new.' éléments créés, '.$link.' éléments liés.</p><hr>';

?>