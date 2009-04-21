<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $g;

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
    
    $cip = ereg_replace('[\s\.\\'.chr(160).'A-z.-]', '', $data[0]); // 160: espace insécable

    // if there is more than 1 column and the first isn't empty
    if ($num > 0 && ($cip != '') && is_numeric($cip) && (strlen($cip) == 7)) {
      $livret = new CBcbProduitLivretTherapeutique();
      $livretLoad = new CBcbProduitLivretTherapeutique();
      
      $i = 0;
      $livret->distObj->Code            = $g;
      $livret->distObj->CIP             = $cip; $i++;
      $livret->distObj->PrixHopital     = $data[$i]?$data[$i]:null; $i++;
      $livret->distObj->PrixVille       = $data[$i]?$data[$i]:null; $i++;
      $livret->distObj->DatePrixHopital = $data[$i]?$data[$i]:null; $i++;
      $livret->distObj->DatePrixVille   = $data[$i]?$data[$i]:null; $i++;
      $livret->distObj->Commentaire     = $data[$i]?$data[$i]:null; $i++;
      $livret->distObj->CodeInterne     = $data[$i]?$data[$i]:null; $i++;
        
      // If the product exists
      if($livretLoad->load($cip)) {
    	  $livret->updateDBFields();
    
    	  if($livret->distObj->Update() >= 0){
          $AppUI->setMsg("Element mis à jour");
        }
      } 
      
      else {
        if($livret->distObj->Insert() >= 0){
          $AppUI->setMsg("Element inséré");
        }
      }
    }
    
    else {
      $AppUI->setMsg("Code CIP [$data[0]] invalide à la ligne $line", UI_MSG_WARNING);
    }
    $line++;
  }
  
  fclose($csv);
}

/*
$livret = new CBcbProduitLivretTherapeutique();
$msg = $livret->distObj->Synchronize();
if ($msg != 1) {
  $AppUI->setMsg($livret->distObj->GetLastError(), UI_MSG_ERROR);
} else {
  $AppUI->setMsg("Synchronisation effectuée avec BCB GES");
}
*/
$AppUI->redirect('m=dPmedicament&a=vw_bcb_import&dialog=1');
?>