<?php
/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * The CDestinataireHprim class
 */
class CDestinataireHprim extends CMbObject {
  // DB Table key
  var $dest_hprim_id  = null;
  
  // DB Fields
  var $destinataire   = null;
  var $type           = null;
  var $url            = null;
  var $username       = null;
  var $password       = null;
  var $actif          = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'destinataire_hprim';
    $spec->key   = 'dest_hprim_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["destinataire"] = "str notNull";
    $specs["type"]         = "enum notNull list|cip|sip default|cip";
    $specs["url"]          = "text notNull";
    $specs["username"]     = "str notNull";
    $specs["password"]     = "password";
    $specs["actif"]        = "bool notNull";
    return $specs;
  }
}
?>