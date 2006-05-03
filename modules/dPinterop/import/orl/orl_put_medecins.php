<?php /* $Id: orl_put_medecins.php,v 1.2 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: 1.2 $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass('dPpatients', 'medecin') );

set_time_limit( 1800 );

$sql = "SELECT * FROM import_medecins";
$listImport = db_loadlist($sql);

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
  $match = db_loadlist($sql);
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
    db_exec($sql);
    $new++;
  } else {
    $sql = "UPDATE import_medecins" .
    		"\nSET mb_id = '".$match[0]["medecin_id"]."'" .
    		"\nWHERE medecin_id = '".$value["medecin_id"]."'";
    db_exec($sql);
    $link++;
  }
  $total++;
}

echo '<p>Opération terminée.</p>';
echo '<p>'.$new.' éléments créés, '.$link.' éléments liés.</p><hr>';

?>