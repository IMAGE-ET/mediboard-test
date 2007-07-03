<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

/**
 * The CFavoriCCAM Class
 */
class CFavoriCCAM extends CMbObject {
  
  var $object_class = null;
  var $favoris_id   = null;
  var $favoris_user = null;
  var $favoris_code = null;
  
  var $_ref_code = null;

  function CFavoriCCAM() {
	$this->CMbObject("ccamfavoris", "favoris_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
  	$specs["favoris_user"] = "notNull ref class|CUser";
  	$specs["favoris_code"] = "notNull str length|7";
  	$specs["object_class"] = "notNull str";
  	return $specs;
  }
  
  function getSeeks() {
    return array (
      "favoris_code" => "equal"
    );
  }
  
  function loadRefsFwd() {
    $this->_ref_code = new CCodeCCAM($this->favoris_code);
    $this->_ref_code->loadLite();
    $this->_ref_code->loadChaps();
  }
  
  function getOrdered($user_id = 0) {
    $listOrdered = array();
    if($user_id) {
      $where["favoris_user"] = "= '$user_id'";
      $order = "favoris_code";
      $fav = new CFavoriCCAM();
      $listFav = $fav->loadList($where, $order);
      foreach($listFav as $key => $curr_fav) {
        $code = new CCodeCCAM($curr_fav->favoris_code);
        $code->loadLite();
        $code->loadChaps();
        $code->favoris_id = $curr_fav->favoris_id;
        $code->occ = 0;
        $chapitre =& $code->chapitres[0];
        $listOrdered[$chapitre["code"]]["nom"] = $chapitre["nom"];
        //$listOrdered[$chapitre["code"]]["codes"] = $code;
        $listOrdered[$chapitre["code"]]["codes"][$curr_fav->favoris_code] = $code;
      }
    }
    return $listOrdered;
    
  }
}
?>