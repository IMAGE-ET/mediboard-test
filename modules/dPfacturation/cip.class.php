<?php
/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * The CCip class
 */
class CCip extends CMbObject {
  // DB Table key
  var $cip_id        = null;
  
  // DB Fields
  var $client_id     = null;
  var $tag           = null;
  var $url           = null;
  var $login         = null;
  var $password      = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'cip';
    $spec->key   = 'cip_id';
    return $spec;
  }
  
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["client_id"]      = "str notNull";
    $specs["tag"]            = "str notNull";
    $specs["url"]            = "text notNull";
    $specs["login"]          = "str notNull";
    $specs["password"]       = "password notNull";
    return $specs;
  }
}
?>