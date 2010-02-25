<?php /* $Id$*/

/**
 * @package Mediboard
 * @subpackage dPImeds
 * @version $Revision$
 * @author Thomas Despoix
 */

class CImeds {
  
  static $soap_path = "/dllimeds/webimeddll.asmx";
  
  /**
   * Url du dossier altéré par le protocole en cours
   * @return string Url
   */
  static function getDossierUrl() {
    global $AppUI;
    $url = $AppUI->_is_intranet ? CAppUI::conf("dPImeds url") : CAppUI::conf("dPImeds remote_url");
    if (!$url) {
      return $url;
    }
    
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
   * Url du service web 
   * @return string Url
   */
  static function getSoapUrl() {
    $url = null;
    
    // Url spécifique
    if ($url = CAppUI::conf("dPImeds soap_url")) {
      $url .= CImeds::$soap_path;
    }
    
    // URL du serveur
    elseif ($url = CAppUI::conf("dPImeds url")) {
		  $parsed = parse_url($url);
		  $parsed['path'] = CImeds::$soap_path;
		  $url = make_url($parsed);
    }

	  return $url;
  }
  
  /**
   * Identifiants Imeds
   * @return array
   */
  static function getIdentifiants() {		
    $ids = array();
    
		// Chargement des id externes
		$etablissement = CGroups::loadCurrent();
		
		$id400 = new CIdSante400;
		$id400->loadLatestFor($etablissement, "Imeds cidc");
		$ids["cidc"] = $id400->id400;
		$id400 = new CIdSante400;
		$id400->loadLatestFor($etablissement, "Imeds cdiv");
		$ids["cdiv"] = $id400->id400;
		$id400 = new CIdSante400;
		$id400->loadLatestFor($etablissement, "Imeds csdv");
		$ids["csdv"] = $id400->id400;
		
		// Chargement des id externes du user courant
    global $AppUI;

    $id400 = new CIdSante400();
		$id400->loadLatestFor($AppUI->_ref_user, "Imeds_login");
	  $ids["login"] = $id400->id400;
		$id400 = new CIdSante400();
		$id400->loadLatestFor($AppUI->_ref_user, "Imeds_password");
		$ids["password"] = md5($id400->id400);
		
		return $ids;
  }
}
?>