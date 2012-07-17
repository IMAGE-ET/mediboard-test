<?php
/**
 * $Id: CCorrespondantCourrier.class.php $
 * 
 * @package    Mediboard
 * @subpackage dPcompteRendu
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $ 
 */

/**
 * Gestion de correpondants dans les documents
 */

class CCorrespondantCourrier extends CMbMetaObject {
  // DB Table key
  var $correspondant_courrier_id = null;

  // DB References
  var $compte_rendu_id = null;
  
  // DB Fields
  var $tag             = null;
  var $quantite        = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'correspondant_courrier';
    $spec->key   = 'correspondant_courrier_id';
    
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["compte_rendu_id"] = "ref class|CCompteRendu notNull cascade";
    $specs["object_class"] = "enum list|CMedecin|CPatient|CCorrespondantPatient notNull";
    $specs["quantite"]     = "num pos notNull min|1 default|1";
    $specs["tag"]          = "str";
    
    return $specs;
  }
}

?>