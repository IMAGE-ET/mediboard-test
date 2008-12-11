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

$step = 100;

$current = mbGetValueFromGet("current", 0);
$new = mbGetValueFromGet("new", 0);
$link = mbGetValueFromGet("link", 0);

$sql = "SELECT * FROM import_patients LIMIT ".($current*$step).", $step";
$listImport = $ds->loadlist($sql);

foreach($listImport as $key => $value) {
  $tmpNom = addslashes(trim($value["nom"]));
  if($tmpNom == '') $tmpNom = "-";
  $tmpPrenom = addslashes(trim($value["prenom"]));
  if($tmpPrenom == '') $tmpPrenom = "-";
  $sql = "SELECT patient_id FROM patients
		  		WHERE patients.nom = '$tmpNom'
		  		AND patients.prenom = '$tmpPrenom'
		  		AND patients.naissance = '".$value["naissance"]."'";
  $match = $ds->loadlist($sql);
  if(!count($match)) {
  	$pat = new CPatient;
  	// DB Table key
    $pat->patient_id = '';
    // DB Fields
    if(trim($value["nom"]) == "")
      $pat->nom= "-";
    else
      $pat->nom = trim($value["nom"]);
    $pat->nom_jeune_fille = trim($value["nom_jeune_fille"]);
    if(trim($value["prenom"]) == "")
      $pat->prenom = "-";
	  else
	    $pat->prenom = trim($value["prenom"]);
    $pat->naissance = $value["naissance"];
    $pat->sexe = $value["sexe"];
    $pat->adresse = $value["adresse"];
    $pat->ville = $value["ville"];
    $pat->cp = $value["cp"];
    $pat->tel = $value["tel"];
    $pat->tel2 = $value["tel2"];
    
    if($value["medecin_traitant"]) {
      $sql = "SELECT medecin_id, mb_id
		      		FROM import_medecins
		      		WHERE medecin_id = '".$value["medecin_traitant"]."'";
      $med = $ds->loadHash($sql);
      $pat->medecin_traitant = $med["mb_id"];
    }
    
    $pat->incapable_majeur = $value["incapable_majeur"];
    $pat->ATNC = $value["ATNC"];
    $pat->matricule = $value["matricule"];
    $pat->rques = $value["rques"];
    echo $pat->store();
    
    for($i = 1; $i <= 3; $i++) {
      if($value["medecin$i"]) {
        $sql = "SELECT medecin_id, mb_id
                FROM dermato_import_medecins
                WHERE medecin_id = '".$value["medecin$i"]."'";
        $med = $ds->loadHash($sql);
        $corresp = new CCorrespondant();
        $corresp->medecin_id = $med["mb_id"];
        $corresp->patient_id = $pat->_id;
        echo $corresp->store();
      }
    }
    
    $sql = "UPDATE import_patients
		    		SET mb_id = '".$pat->patient_id."'
		    		WHERE patient_id = '".$value["patient_id"]."'";
    $ds->exec($sql);
    $new++;
  } else {
    $sql = "UPDATE import_patients
		    		SET mb_id = '".$match[0]["patient_id"]."'
		    		WHERE patient_id = '".$value["patient_id"]."'";
    $ds->exec($sql);
    $link++;
  }
}

$current++;

echo '<p>Opération terminée.</p>';
echo '<p>'.$new.' éléments créés, '.$link.' éléments liés.</p><hr>';

if(count($listImport) == $step) {
  echo '<a onclick="javascript:next();">'.(count($listImport)).' suivant >>></a>';
  ?>
  <script type="text/javascript">
    function next() {
      var url = "index.php?m=dPinterop&dialog=1&a=put_patients";
      url += "&current=<?php echo $current; ?>";
      url += "&new=<?php echo $new; ?>";
      url += "&link=<?php echo $link; ?>";
      window.location.href = url;
    }
    next();
  </script>
  <?php

}

?>
