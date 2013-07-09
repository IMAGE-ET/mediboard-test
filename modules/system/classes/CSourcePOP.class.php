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
 * Class CSourcePOP
 */
class CSourcePOP extends CExchangeSource {

  /* KEY */
  public $source_pop_id;

  // DB Fields
  public $host;
  public $port;
  public $auth_ssl;
  public $timeout; //seconds
  public $type;
  public $libelle;

  public $last_update;
  public $object_class;
  public $object_id;

  public $_mailbox; //ressource id
  public $_server; //string of server for imap
  public $_mailbox_info;

  /**
   * table spec
   *
   * @return CMbObjectSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_pop';
    $spec->key   = 'source_pop_id';
    return $spec;
  }

  /**
   * field properties
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["host"]              = "str autocomplete";
    $props["port"]              = "num default|25";
    $props["auth_ssl"]          = "enum list|None|SSL/TLS|STARTTLS";
    $props["password"]          = "password show|0";
    $props["timeout"]           = "num default|5 max|30";
    $props["type"]              = "enum list|pop3|imap";
    $props["libelle"]           = "str notNull";

    $props["last_update"]       = "dateTime";
    $props["object_id"]         = "ref notNull class|CMbObject meta|object_class";
    $props["object_class"]      = "str notNull class show|0";
    $props["_server"]           = "str maxLength|255";
    return $props;
  }

  /**
   * @return CMbObject|CMediusers
   */
  function loadRefMetaObject() {
    if ($this->object_class == "CMediusers") {
      $this->_ref_mediuser = CMbMetaObject::loadFromGuid("$this->object_class-$this->object_id");
      $this->_ref_mediuser->loadRefFunction();
      return $this->_ref_mediuser;
    }
    else {
      $this->_ref_metaobject = CMbMetaObject::loadFromGuid("$this->object_class-$this->object_id");
      return $this->_ref_metaobject;
    }
  }

  /**
   * get back props
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["user_mail_account"]            = "CUserMail account_id";
    return $backProps;
  }

}