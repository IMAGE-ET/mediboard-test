<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPetablissement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * External group class (Etablissement externe)
 */
class CEtabExterne extends CMbObject {
  public $etab_id;  

  // DB Fields
  public $nom;
  public $raison_sociale;
  public $adresse;
  public $cp;
  public $ville;
  public $tel;
  public $fax;
  public $finess;
  public $siret;
  public $ape;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'etab_externe';
    $spec->key   = 'etab_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["transferts_sortie"] = "CSejour etablissement_sortie_id";
    $backProps["transferts_entree"] = "CSejour etablissement_entree_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["nom"]            = "str notNull confidential seekable";
    $props["raison_sociale"] = "str maxLength|50";
    $props["adresse"]        = "text confidential";
    $props["cp"]             = "numchar length|5";
    $props["ville"]          = "str maxLength|50 confidential";
    $props["tel"]            = "phone";
    $props["fax"]            = "phone";
    $props["finess"]         = "numchar length|9 confidential mask|9xS9S99999S9 control|luhn";
    $props["siret"]          = "str length|14";
    $props["ape"]            = "str maxLength|6 confidential";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields () {
    parent::updateFormFields();
    $this->_view = $this->nom; 
  }
}
