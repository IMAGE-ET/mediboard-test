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

  var $_mailbox       = null; //ressource id
  var $_server        = null; //string of server for imap
  var $_mailbox_info  = null;


  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_pop';
    $spec->key   = 'source_pop_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["host"]          = "str";
    $props["port"]          = "num default|25";
    $props["auth_ssl"]      = "enum list|None|SSL/TLS|STARTTLS";
    $props["password"]      = "password show|0";
    $props["timeout"]       = "num default|5 max|30";
    $props["type"]          = "enum list|pop3|imap";
    $props["libelle"]       = "str";
    $props["_server"]       = "str maxLength|255";
    return $props;
  }

  /**
   *
   *
   * @param $options
   *
   * @return array
   */
  function mailboxListHeaders() {
    return imap_headers($this->_mailbox);
  }

  /**
   * get the body of the mail
   *
   * @param $id
   *
   * @return string
   */
  function mailboxBody($id) {
    return imap_body($this->_mailbox, $id);
  }

  /**
   * open the structure of a body
   * @param $msg_id
   * @param $part
   *
   * @return object
   */
  function mailboxOpenPart($msg_id, $part){
    return imap_fetchbody($this->_mailbox, $msg_id, $part);
  }


  /**
   * get the body's text of the mail (no PJ)
   *
   * @param      $msg_number  message number
   * @param      $mime_type
   * @param bool $structure
   * @param bool $part_number
   *
   * @return string
   *
   */
  function mailboxGetText($msg_number, $mime_type="TEXT/HTML", $structure = false, $part_number = false) {
    if (!$structure) {
      $structure = self::mailboxStructure($msg_number);
    }

    if($structure) {

      if($mime_type == self::get_mime_type($structure)) {
        if(!$part_number) {
          $part_number = "1";
        }
        $text = self::mailboxOpenPart($msg_number, $part_number);

        if($structure->encoding == 3) {
          return imap_base64($text);
        } else if ($structure->encoding == 4) {
          return imap_qprint($text);
        } else {
          return $text;
        }
      }

      //switch structure type
      switch($structure->type) {
        //0:text, 1:multipart, 2:message, 3:application, 4:audio,5:image,6:video,7;other

        case(0):  //text
            return self::mailboxGetText($msg_number, "TEXT/PLAIN", $structure, 0);
          break;


        case(1):  //multipart
            while (list($index, $sub_structure) = each($structure->parts)) {
              if ($part_number) {
                $prefix = $part_number . '.';
              } else {
                $prefix = null;
              }
              $data = self::mailboxGetText($msg_number, $mime_type, $sub_structure, $prefix . ($index + 1));
              mbTrace($data);
              if ($data) {
                return $data;
              }
            }
          break;

        case(5):

          break;

        default:
          break;
      }
    }
    return false;
  }

  /**
   * get the mime type of the structure
   *
   * @param $structure
   *
   * @return string
   */
  function get_mime_type(&$structure) {
    $primary_mime_type = array("TEXT", "MULTIPART","MESSAGE", "APPLICATION", "AUDIO","IMAGE", "VIDEO", "OTHER");
    if($structure->subtype) {
      return $primary_mime_type[(int) $structure->type] . '/' .$structure->subtype;
    }
    return "TEXT/PLAIN";
  }

}