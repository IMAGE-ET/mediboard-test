<?php
/**
 * $Id: CCompteRendu.class.php 19055 2013-05-07 14:09:27Z mytto $
 *
 * @package    Mediboard
 * @subpackage CompteRendu
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19055 $
 */

/**
 * Gestion de correpondants dans les documents
 */
class CCorrespondantCourrier extends CMbMetaObject {
  // DB Table key
  public $correspondant_courrier_id;

  // DB References
  public $compte_rendu_id;
  
  // DB Fields
  public $tag;
  public $quantite;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'correspondant_courrier';
    $spec->key   = 'correspondant_courrier_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["compte_rendu_id"] = "ref class|CCompteRendu notNull cascade";
    $specs["object_class"] = "enum list|CMedecin|CPatient|CCorrespondantPatient notNull";
    $specs["quantite"]     = "num pos notNull min|1 default|1";
    $specs["tag"]          = "str";
    return $specs;
  }
}
