<?php /* $Id: dermato_put_patients.php,v 1.2 2006/04/21 16:56:07 mytto Exp $ */

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

require_once( $AppUI->getModuleClass('dPpatients', 'patients') );

set_time_limit( 1800 );

$step = 100;

$current = mbGetValueFromGet("current", 0);
$new = mbGetValueFromGet("new", 0);
$link = mbGetValueFromGet("link", 0);

$sql = "SELECT * FROM dermato_import_patients" .
    "\nLIMIT ".($current*$step).", $step";
$listImport = db_loadlist($sql);

foreach($listImport as $key => $value) {
  $tmpNom = addslashes(trim($value["nom"]));
  if($tmpNom == '') $tmpNom = "-";
  $tmpPrenom = addslashes(trim($value["prenom"]));
  if($tmpPrenom == '') $tmpPrenom = "-";
  $sql = "SELECT patient_id FROM patients" .
  		"\nWHERE patients.nom = '$tmpNom'" .
  		"\nAND patients.prenom = '$tmpPrenom'" .
  		"\nAND patients.naissance = '".$value["naissance"]."'";
  $match = db_loadlist($sql);
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
    if($pat->medecin_traitant) {
      $sql = "SELECT medecin_id" .
      		"FROM dermato_import_medecins" .
      		"WHERE medecin_id = '".$value["medecin_traitant"]."'";
      $med = db_loadlist($sql);
      $pat->medecin_traitant = $med[0]["mb_id"];
    }
    if($pat->medecin1) {
      $sql = "SELECT medecin_id" .
      		"FROM dermato_import_medecins" .
      		"WHERE medecin_id = '".$value["medecin1"]."'";
      $med = db_loadlist($sql);
      $pat->medecin1 = $med[0]["mb_id"];
    }
    if($pat->medecin2) {
      $sql = "SELECT medecin_id" .
      		"FROM dermato_import_medecins" .
      		"WHERE medecin_id = '".$value["medecin2"]."'";
      $med = db_loadlist($sql);
      $pat->medecin2 = $med[0]["mb_id"];
    }
    if($pat->medecin3) {
      $sql = "SELECT medecin_id" .
      		"FROM dermato_import_medecins" .
      		"WHERE medecin_id = '".$value["medecin3"]."'";
      $med = db_loadlist($sql);
      $pat->medecin3 = $med[0]["mb_id"];
    }
    $pat->incapable_majeur = $value["incapable_majeur"];
    $pat->ATNC = $value["ATNC"];
    $pat->matricule = $value["matricule"];
    $pat->SHS = null;
    $pat->rques = $value["rques"];
    echo $pat->store();
    $sql = "UPDATE dermato_import_patients" .
    		"\nSET mb_id = '".$pat->patient_id."'" .
    		"\nWHERE patient_id = '".$value["patient_id"]."'";
    db_exec($sql);
    $new++;
  } else {
    $sql = "UPDATE dermato_import_patients" .
    		"\nSET mb_id = '".$match[0]["patient_id"]."'" .
    		"\nWHERE patient_id = '".$value["patient_id"]."'";
    db_exec($sql);
    $link++;
  }
}

$current++;

echo '<p>Opération terminée.</p>';
echo '<p>'.$new.' éléments créés, '.$link.' éléments liés.</p><hr>';

if(count($listImport) == $step) {
  echo '<a onclick="javascript:next();">'.(count($listImport)).' suivant >>></a>';
  ?>
  <script language="JavaScript" type="text/javascript">
    function next() {
      var url = "index.php?m=dPinterop&dialog=1&a=dermato_put_patients";
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
