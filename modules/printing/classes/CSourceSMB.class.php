<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Printing
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Source SMB
 */
class CSourceSMB extends CSourcePrinter {
  // DB Table key
  public $source_smb_id;
  
  // DB Fields
  public $user;
  public $password;
  public $workgroup;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_smb';
    $spec->key   = 'source_smb_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["user"]         = "str";
    $props["password"]     = "password revealable";
    $props["workgroup"]    = "str";

    return $props;
  }

  /**
   * @see parent::sendDocument()
   */
  function sendDocument($file) {
    $smb = new CSMB;
    $smb->init($this);
    $smb->printFile($file);
  }
}
