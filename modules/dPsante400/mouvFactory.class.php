<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPsante400
* @version $Revision: $
* @author Thomas Despoix
*/

// Mouvement factory

class CMouvFactory {
  static $matrix = array (
    "default" => array (),
    "medicap" => array (
      "sejour" => "CMouvSejourEcap",
      "intervention" => "CMouvInterventionECap",
      "attendue" => "CMouvAttendueECap",
    ),
    "tonkin" => array (
      "sejour" => "CMouvSejourTonkin"
    ),
  );

  /**
   * Get available types for current compat config
   * @return array the types, null for invalid compat
   */
  static function getTypes() {
    global $dPconfig;
		if (null == $mode_compat = $dPconfig["interop"]["mode_compat"]) {
		  trigger_error("Mode de compatibilité non initalisé", E_USER_ERROR);
		  return;
		}
		
		if (!array_key_exists($mode_compat, self::$matrix)) {
		  trigger_error("Mode de compatibilité '$mode_compat' non géré", E_USER_ERROR);
		  return;  
		}

		return array_keys(self::$matrix[$mode_compat]);
  }
  
  /**
   * Create a mouvement instance from given type
   *
   * @param string $type
   * @return CMouvement400 concrete instance, null for unhandled type
   */
  static function create($type) {
    global $dPconfig;
    $mode_compat = $dPconfig["interop"]["mode_compat"];
    if (null == $class = @self::$matrix[$mode_compat][$type]) {
      trigger_error("Pas de gestionnaire en mode de compatibilité '$mode_compat' et type de mouvement '$type'", E_USER_ERROR);
    }
    
    return new $class;
  }
}

?>