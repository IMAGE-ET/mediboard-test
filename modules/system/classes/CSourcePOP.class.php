<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
class CSourcePOP extends CExchangeSource {

  /* KEY */
  var $source_pop_id = null;

  // DB Fields
  var $host           = null;
  var $port           = null;
  var $auth_ssl       = null;
  var $timeout        = null; //seconds
  var $type           = null;
  var $libelle        = null;

  var $last_update    = null;
  var $object_class   = null;
  var $object_id      = null;

  var $_mailbox       = null; //ressource id
  var $_server        = null; //string of server for imap
  var $_mailbox_info  = null;

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
    $props["host"]          = "str autocomplete";
    $props["port"]          = "num default|25";
    $props["auth_ssl"]      = "enum list|None|SSL/TLS|STARTTLS";
    $props["password"]      = "password show|0";
    $props["timeout"]       = "num default|5 max|30";
    $props["type"]          = "enum list|pop3|imap";
    $props["libelle"]       = "str notNull";

    $props["last_update"]   = "dateTime";
    $props["object_id"]     = "ref notNull class|CMbObject meta|object_class";
    $props["object_class"]  = "str notNull class show|0";
    $props["_server"]       = "str maxLength|255";
    return $props;
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