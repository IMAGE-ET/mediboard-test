<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $g;

set_time_limit(360);

$type_import = CValue::post("type_import");

// Recuperation du fichier
$file = $_FILES['datafile'];

if (strtolower(pathinfo($file['name'] , PATHINFO_EXTENSION) == 'csv')) {
  $csv = fopen($file['tmp_name'], 'r');
  
  $line = 1;
  while (($data = fgetcsv($csv, 2000, ',')) !== false) {
    $num = count($data);
    for ($c = 0; $c < $num; $c++) {
      $data[$c] = trim($data[$c]);
    }
    
    $identifiant = preg_replace('/[\s\.\\'.chr(160).'A-z.-]/', '', $data[0]); // 160: espace insécable

    // if there is more than 1 column and the first isn't empty
    if ($num > 0 && ($identifiant != '') && is_numeric($identifiant) && (strlen($identifiant) == 7)) {
      $identifiants = array();
      if($type_import == "cip"){
        // Import par cip
        $identifiants[] = $identifiant;  
      } else {
        // Import par UCD
        $_produits = CBcbProduit::getProduitsFromUCD($identifiant);
        foreach($_produits as $_produit){
          $identifiants[] = $_produit["CODE_CIP"];
        }
        if(count($identifiants) == 0){
          CAppUI::setMsg("Aucun produit ne correspond au code UCD [$identifiant]", UI_MSG_WARNING);
        }
      }
      // Parcours de tous les codes
      foreach($identifiants as $_identifiant){
	      $livret = new CBcbProduitLivretTherapeutique();
	      $livretLoad = new CBcbProduitLivretTherapeutique();
	      
	      $i = 0;
	      $livret->distObj->Code            = $g;
	      $livret->distObj->CIP             = $_identifiant; $i++;
	      $livret->distObj->PrixHopital     = isset($data[$i])?$data[$i]:null; $i++;
	      $livret->distObj->PrixVille       = isset($data[$i])?$data[$i]:null; $i++;
	      $livret->distObj->DatePrixHopital = isset($data[$i])?$data[$i]:null; $i++;
	      $livret->distObj->DatePrixVille   = isset($data[$i])?$data[$i]:null; $i++;
	      $livret->distObj->Commentaire     = isset($data[$i])?$data[$i]:null; $i++;
	      $livret->distObj->CodeInterne     = isset($data[$i])?$data[$i]:null; $i++;
	      
	      // If the product exists
	      if($livretLoad->load($_identifiant)) {
	    	  $livret->updateDBFields();
	    	  if($livret->distObj->Update() >= 0){
	          CAppUI::setMsg("Element mis à jour");
	        }
	      }
	      else {
	        $produit = new CBcbProduit();
	        $produit->load($_identifiant);
	        if($produit->code_cip){
		        if($livret->distObj->Insert() >= 0){
		          CAppUI::setMsg("Element inséré");
						}
	        } else {
	        	 CAppUI::setMsg("Aucun produit ne correspond au code CIP [$identifiant]", UI_MSG_WARNING);
	        }
	      }
      }
    }
    else {
      CAppUI::setMsg("Code [$data[0]] invalide à la ligne $line", UI_MSG_WARNING);
    }
    $line++;
  }  
  fclose($csv);
}

CAppUI::redirect('m=dPmedicament&a=vw_bcb_import&dialog=1');
?>