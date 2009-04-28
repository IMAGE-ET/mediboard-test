<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CEtabExterne extends CMbObject {
  // DB Table key
	var $etab_id       = null;	

  // DB Fields
	var $nom            = null;
  var $raison_sociale = null;
  var $adresse        = null;
  var $cp             = null;
  var $ville          = null;
  var $tel            = null;
  var $fax            = null;
  var $finess         = null;
  var $siret          = null;
  var $ape            = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'etab_externe';
    $spec->key   = 'etab_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["transferts"] = "CSejour etablissement_transfert_id";
    return $backProps;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["nom"]            = "str notNull confidential seekable";
    $specs["raison_sociale"] = "str maxLength|50";
    $specs["adresse"]        = "text confidential";
    $specs["cp"]             = "numchar length|5";
    $specs["ville"]          = "str maxLength|50 confidential";
    $specs["tel"]            = "numchar length|10 mask|99S99S99S99S99";
    $specs["fax"]            = "numchar length|10 mask|99S99S99S99S99";
    $specs["finess"]         = "numchar length|9";
    $specs["siret"]          = "str length|14";
    $specs["ape"]            = "str maxLength|6 confidential";
    return $specs;
  }
  
  function updateFormFields () {
    parent::updateFormFields();
    $this->_view = $this->nom; 
  }
}
?>