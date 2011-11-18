<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$livret = new CBcbProduitLivretTherapeutique();

$code_cip          = CValue::post("code_cip");
$prix_hopital      = CValue::post("prix_hopital");
$date_prix_hopital = CValue::post("date_prix_hopital");
$prix_ville        = CValue::post("prix_ville");
$date_prix_ville   = CValue::post("date_prix_ville");
$code_interne      = CValue::post("code_interne");
$commentaire       = CValue::post("commentaire");
$function_guid     = CValue::post("_function_guid", null);
$unite_prise       = CValue::post("_unite_prise");

// Recherche du produit dans la table du livret therapeutique et sauvegarde de l'unite de prise
$produit_livret_thera = new CProduitLivretTherapeutique();
$produit_livret_thera->code_cip = $code_cip;
$produit_livret_thera->group_id = CGroups::loadCurrent()->_id;
$produit_livret_thera->loadMatchingObject();

// Chargement du code cis et code ucd du produit
$produit_livret_thera->loadRefProduit();
$produit_livret_thera->code_ucd = $produit_livret_thera->_ref_produit->code_ucd;
$produit_livret_thera->code_cis = $produit_livret_thera->_ref_produit->code_cis;

$produit_livret_thera->unite_prise = $unite_prise;
$produit_livret_thera->store();

if (isset($function_guid)) {
  $crc = $function_guid;
}
else {
  $crc = CProductStockGroup::getHostGroup(false)->_guid;
}

$crc = CBcbProduit::getHash($crc);

$del = CValue::post("del");

if($del == 1){ 
  // Suppression du produit
  if($livret->distObj->Delete($crc, $code_cip) < 0){
    // Affichage de l'erreur
    CAppUI::setMsg("Produit supprimé du livret thérapeutique".$livret->distObj->GetLastError(), UI_MSG_ERROR );
  } else {
    CAppUI::setMsg("Produit supprimé du livret thérapeutique", UI_MSG_OK );
  }  
} else {
  $produitLivret = new CBcbProduitLivretTherapeutique();

  if($produitLivret->load($code_cip, $crc) && $produitLivret->group_id == $crc){
    
    // Si le produit existe, on lance l'update
    $livret->distObj->Code = $crc;
	  $livret->distObj->CIP = $code_cip;
	  $livret->distObj->PrixHopital = $prix_hopital;
	  $livret->distObj->PrixVille = $prix_ville;
	  $livret->distObj->DatePrixHopital = $date_prix_hopital;
	  $livret->distObj->DatePrixVille = $date_prix_ville;
	  $livret->distObj->Commentaire = $commentaire;
	  $livret->distObj->CodeInterne = $code_interne;
    
	  
	  $livret->updatePlainFields();

	  if($livret->distObj->Update() < 0){
      // Affichage de l'erreur
      CAppUI::setMsg("Produit modifié".$livret->distObj->GetLastError(), UI_MSG_ERROR );
    } else {
      CAppUI::setMsg("Produit modifié", UI_MSG_OK );
    }    
  } else {
    // on crée le produit
		$livret->distObj->Code = $crc;
		
		$livret->distObj->CIP = $code_cip;

    if($livret->distObj->Insert() < 0){
      
      // Affichage de l'erreur
      CAppUI::setMsg("Produit ajouté au livret thérapeutique".$livret->distObj->GetLastError(), UI_MSG_ERROR );
    } else {
      CAppUI::setMsg("Produit ajouté au livret thérapeutique", UI_MSG_OK );
      
      $produitLivret = new CBcbProduitLivretTherapeutique();
      $produitLivret->load($code_cip, $crc);
      if ($produitLivret->addToStocks()) {
        CAppUI::setMsg("Produit ajouté aux stocks", UI_MSG_OK );
      }
    }
  }
}
 
echo CAppUI::getMsg();
CApp::rip();
?>