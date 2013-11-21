<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Class CCirconstance
 */
class CCirconstance extends CMbObject {
  public $circonstance_id;
  
  // DB Fields
  public $code;
  public $libelle;
  public $commentaire;
  public $actif;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'circonstance';
    $spec->key   = 'circonstance_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["code"]        = "str notNull";
    $props["libelle"]     = "str notNull seekable";
    $props["commentaire"] = "text notNull seekable";
    $props["actif"]       = "bool";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["RPU"] = "CRPU circonstance";
    return $backProps;
  }
}
