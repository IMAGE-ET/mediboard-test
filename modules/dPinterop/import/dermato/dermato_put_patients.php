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

$step = 5000;

$current = CValue::get("current", 0);
$new = CValue::get("new", 0);
$link = CValue::get("link", 0);

$sql = "SELECT * FROM dermato_import_patients LIMIT ".($current*$step).", $step";
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
    
    if(trim($value["prenom"]) == "")
      $pat->prenom = "-";
	  else
	    $pat->prenom = trim($value["prenom"]);
	    
	  $pat->nom_jeune_fille = trim($value["nom_jeune_fille"]);
    $pat->naissance = $value["naissance"];
    $pat->sexe = $value["sexe"];
    $pat->adresse = $value["adresse"];
    $pat->ville = $value["ville"];
    $pat->cp = $value["cp"];
    
    if(!($pat->cp = (int)$pat->cp)) {
      $pat->cp = "";
    }
    if($pat->cp != "" && strlen($pat->cp) < 5) {
      while(strlen($pat->cp) < 5) {
        $pat->cp = "0".$pat->cp;
      }
    }
    $pat->tel = $value["tel"];
    if(!($pat->tel = (int)$pat->tel)) {
      $pat->tel = "";
    }
    if($pat->tel != "" && strlen($pat->tel) < 10) {
      while(strlen($pat->tel) < 10) {
        $pat->tel = "0".$pat->tel;
      }
    }
    $pat->tel2 = $value["tel2"];
    if(!($pat->tel2 = (int)($pat->tel2))) {
      $pat->tel2 = "";
    }
    if($pat->tel2 != "" && strlen($pat->tel2) < 10) {
      while(strlen($pat->tel2) < 10) {
        $pat->tel2 = "0".$pat->tel2;
      }
    }
    if($value["medecin_traitant"]) {
      $sql = "SELECT medecin_id, mb_id
		      		FROM dermato_import_medecins
		      		WHERE medecin_id = '".$value["medecin_traitant"]."'";
      $med = $ds->loadHash($sql);
      $pat->medecin_traitant = $med["mb_id"];
    }

    $pat->incapable_majeur = $value["incapable_majeur"] == 'o' ? 1 : 0;
    $pat->ATNC = $value["ATNC"] == 'o' ? 1 : 0;
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
    
    $sql = "UPDATE dermato_import_patients
		    		SET mb_id = '".$pat->patient_id."'
		    		WHERE patient_id = '".$value["patient_id"]."'";
    $ds->exec($sql);
    $new++;
  } else {
    $sql = "UPDATE dermato_import_patients
		    		SET mb_id = '".$match[0]["patient_id"]."'
		    		WHERE patient_id = '".$value["patient_id"]."'";
    $ds->exec($sql);
    $link++;
  }
}

$current++;

echo '<p>Opération terminée.</p>';
echo '<p>'.$new.' éléments créés, '.$link.' éléments liés.</p><hr />';

if(count($listImport) == $step) {
  echo '<a href="#" class="tooltip-trigger" onclick="javascript:nextStep();">'.(count($listImport)).' suivant >>></a>';
  ?>
  <script type="text/javascript">
    function nextStep() {
      var url = "index.php?m=dPinterop&dialog=1&u=import/dermato&a=dermato_put_patients";
      url += "&current=<?php echo $current; ?>";
      url += "&new=<?php echo $new; ?>";
      url += "&link=<?php echo $link; ?>";
      window.location.href = url;
    }
    nextStep();
  </script>
  <?php

}

?>
