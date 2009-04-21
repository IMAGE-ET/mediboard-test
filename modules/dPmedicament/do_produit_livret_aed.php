<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision$
* @author Alexis Granger
*/


global $AppUI, $g;

$livret = new CBcbProduitLivretTherapeutique();

$group_id          = mbGetValueFromPost("group_id");
$code_cip          = mbGetValueFromPost("code_cip");
$prix_hopital      = mbGetValueFromPost("prix_hopital");
$date_prix_hopital = mbGetValueFromPost("date_prix_hopital");
$prix_ville        = mbGetValueFromPost("prix_ville");
$date_prix_ville   = mbGetValueFromPost("date_prix_ville");
$code_interne      = mbGetValueFromPost("code_interne");
$commentaire       = mbGetValueFromPost("commentaire");

$del = mbGetValueFromPost("del");

if($del == 1){ 
  // Suppression du produit
  if($livret->distObj->Delete($g, $code_cip) < 0){
    // Affichage de l'erreur
    $AppUI->setMsg("Produit supprimé".$livret->distObj->GetLastError(), UI_MSG_ERROR );
  } else {
    $AppUI->setMsg("Produit supprimé", UI_MSG_OK );
  }  
} else {
  $produitLivret = new CBcbProduitLivretTherapeutique();
  if($produitLivret->load($code_cip)){
    // Si le produit existe, on lance l'update
    $livret->distObj->Code = $g;
	  $livret->distObj->CIP = $code_cip;
	  $livret->distObj->PrixHopital = $prix_hopital;
	  $livret->distObj->PrixVille = $prix_ville;
	  $livret->distObj->DatePrixHopital = $date_prix_hopital;
	  $livret->distObj->DatePrixVille = $date_prix_ville;
	  $livret->distObj->Commentaire = $commentaire;
	  $livret->distObj->CodeInterne = $code_interne;

	  $livret->updateDBFields();

	  if($livret->distObj->Update() < 0){
      // Affichage de l'erreur
      $AppUI->setMsg("Produit modifié".$livret->distObj->GetLastError(), UI_MSG_ERROR );
    } else {
      $AppUI->setMsg("Produit modifié", UI_MSG_OK );
    }    
  } else {
    // on crée le produit
		$livret->distObj->Code = $g;
		$livret->distObj->CIP = $code_cip;
    if($livret->distObj->Insert() < 0){
      // Affichage de l'erreur
      $AppUI->setMsg("Produit créé".$livret->distObj->GetLastError(), UI_MSG_ERROR );
    } else {
      $AppUI->setMsg("Produit créé", UI_MSG_OK );
    }
  }
}
 
echo $AppUI->getMsg();
CApp::rip();
?>