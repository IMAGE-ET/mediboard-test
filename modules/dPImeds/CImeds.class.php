<?php /* $Id:  $*/

/**
 * @package Mediboard
 * @subpackage dPImeds
 * @version $Revision:  $
 * @author Thomas Despoix
 */

class CImeds {
  /**
   * Url du dossier altéré par le protocole en cours
   * @return string
   */
  static function getDossierUrl() {
    $url = CAppUI::conf("dPImeds url");
    $parsed_url = parse_url($url);
    
    if (!isset($_SERVER['HTTP_REFERER'])) {
      return $url;
    }
    
    $base = $_SERVER['HTTP_REFERER'];
    $parsed_base = parse_url($base);
    
    $parsed_url["scheme"] = $parsed_base["scheme"]; 
    $url = make_url($parsed_url);
    return $url;
  }
  
  /**
   * Identifiants Imeds
   * @return array
   */
  static function getIdentifiants() {
		$idImeds = array();
		
		// Chargement des id externes
		$etablissement = CGroups::loadCurrent();
		
		$id400 = new CIdSante400;
		$id400->loadLatestFor($etablissement, "Imeds cidc");
		$idImeds["cidc"] = $id400->id400;
		$id400 = new CIdSante400;
		$id400->loadLatestFor($etablissement, "Imeds cdiv");
		$idImeds["cdiv"] = $id400->id400;
		$id400 = new CIdSante400;
		$id400->loadLatestFor($etablissement, "Imeds csdv");
		$idImeds["csdv"] = $id400->id400;
		
		// Chargement des id externes du user courant
    global $AppUI;

    $id400 = new CIdSante400();
		$id400->loadLatestFor($AppUI->_ref_user, "Imeds_login");
		$idImeds["login"] = $id400->id400;
		$id400 = new CIdSante400();
		$id400->loadLatestFor($AppUI->_ref_user, "Imeds_password");
		$idImeds["password"] = md5($id400->id400);
		
		mbDump($idImeds);
		return $idImeds;
  }
}
?>