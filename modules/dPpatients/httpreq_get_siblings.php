<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision:  $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$patient_id = mbGetValueFromGet("patient_id", null);
$nom        = mbGetValueFromGet("nom"       , null);
$prenom     = mbGetValueFromGet("prenom"    , null);
$naissance  = mbGetValueFromGet("naissance" , "0000-00-00");

$textDifferent = null;
if($patient_id) {
  $oldPat = new CPatient();
  $oldPat->load($patient_id);
  if(!$oldPat->checkSimilar($nom, $prenom)) {
    $textDifferent = "Le nom et/ou le prénom sont très différents de" .
        "\n>> $oldPat->_view" .
        "\nVoulez-vous tout de même sauvegarder ?";
  }
}

$patientSib = new CPatient();
$patientSib->patient_id = $patient_id;
$patientSib->nom        = $nom;
$patientSib->prenom     = $prenom;
$patientSib->naissance  = $naissance;

$siblings = $patientSib->getSiblings();

$textSiblings = null;

if(count($siblings) != 0) {
	$textSiblings = "Risque de doublons :";
  foreach($siblings as $key => $value) {
    $textSiblings .= "\n>> ".$value->nom." ".$value->prenom.
                     " né(e) le ".$value->naissance.
                     " habitant ".$value->adresse." ".$value->cp." ".$value->ville;
  }
  $textSiblings .= "\nVoulez-vous tout de même sauvegarder ?";
}


// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("textDifferent", $textDifferent);
$smarty->assign("textSiblings" , $textSiblings );

$smarty->display("httpreq_get_siblings.tpl");
?>