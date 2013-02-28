<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * The CAlert Class
 */
class CAlert extends CMbMetaObject {
  // DB Table key
  public $alert_id;
  
  // DB Fields
  public $tag;
  public $level;
  public $comments;
  public $handled;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'alert';
    $spec->key   = 'alert_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["tag"]      = "str notNull";
    $props["level"]    = "enum list|low|medium|high default|medium notNull";
    $props["comments"] = "text";
    $props["handled"]  = "bool notNull default|0";
    $props["object_id"] .= " cascade";
    return $props;
  }
}
