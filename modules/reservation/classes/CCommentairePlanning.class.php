<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Classe CCommentairePlanning
 * @ gère les commentaires sous la forme libellé + description dans le planning de réservation
 */
class CCommentairePlanning extends CMbObject {
  // DB Table key
  var $commentaire_planning_id = null;
  
  // DB References
  var $salle_id    = null;
  
  // DB Fields
  var $libelle     = null;
  var $commentaire = null;
  var $color       = null;
  var $debut       = null;
  var $fin         = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'commentaire_planning';
    $spec->key   = 'commentaire_planning_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["salle_id"]    = "ref class|CSalle";
    $specs["libelle"]     = "str notNull";
    $specs["commentaire"] = "text helped";
    $specs["color"]       = "str length|6 default|DDDDDD";
    $specs["debut"]       = "dateTime notNull";
    $specs["fin"]         = "dateTime notNull";
    
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = $this->libelle;
  }
}
