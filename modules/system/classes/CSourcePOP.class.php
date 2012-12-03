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
    $props["_server"]       = "str maxLength|255";
    return $props;
  }

  //MAILBOX FUNCTIONS
  /**
   * Initialisation of the remote connexion to IMAP/POP
   *
   * @return string
   */
  function init() {
    if (!function_exists("imap_open")) {
      CAppUI::stepAjax("bibliothèque IMAP PHP non installée", UI_MSG_ERROR);
    }

    //initialise open TIMEOUT
    if($this->timeout && $this->timeout > 5 ){
      imap_timeout( 1, $this->timeout );
    } else {
      imap_timeout( 1, 5 );
    }


    //lets create the string for stream
    $type = ($this->type == "pop3")?"/".$this->type:"";
    $ssl  = ($this->auth_ssl == "SSL/TLS")?"/ssl/novalidate-cert":"";
    return $this->_server = "{".$this->host.":".$this->port.$type.$ssl."}";
  }

  function getServerString(){
    return $this->_server;
  }

  function setServerString($server){
    $this->_server = $server;
  }

  /**
   * Open the remote mailbox
   *
   * @return ressource
   */
  function mailboxOpen() {

    if(!isset($this->_server)) {
      $this->init();
    }

    if(!$this->_mailbox = @imap_open($this->_server, $this->user, $this->password)) {
      CAppUI::stepAjax("CSourcePOP-error-imap_open", UI_MSG_ERROR);
    }
    $this->_mailbox_info = imap_check($this->_mailbox);
    return $this->_mailbox;
  }

  /**
   * List the root child folders
   *
   * @param        $ref
   * @param string $pattern
   *
   * @return array|bool
   */
  function mailboxList($ref, $pattern="*") {
    if (!$this->_mailbox) {
      return false;
    }
    return imap_list($this->_mailbox, $ref, $pattern);
  }

  /**
   * Get the infos of the mailbox
   *
   * @return basics infos of the mailbox (connexion required)
   */
  function mailboxInfo() {
    return $this->_mailbox_info;
  }

  /**
   *
   *
   * @param $options
   *
   * @return array
   */
  function mailboxListHeaders($options) {
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
   * Open a complete EMAIL : header AND content
   * @param $id
   *
   * @return array
   */
  function mailboxLoadComplete($id) {
    $head     = self::mailboxHeader($id);
    $content  = self::mailboxGetpart($id);
    return array("head" =>$head,"content"=>$content);
  }


  /**
   * get the header of the mail
   *
   * @param $id
   *
   * @return array
   */
  function mailboxHeader($id) {
    return imap_fetch_overview($this->_mailbox, $id);
  }


  /**
   * Open the body structure
   *
   * @param $id
   *
   * @return object
   */
  function mailboxStructure($id){
    return imap_fetchstructure($this->_mailbox, $id);
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
  function mailboxGetpart($msg_number, $mime_type="TEXT/HTML", $structure = false,$part_number = false) {
    $TEXT = null;

    if (!$structure) {
      $structure = self::mailboxStructure($msg_number);
    }

    if($structure) {
      if ($mime_type == self::get_mime_type($structure)) {
        if (!$part_number) {
          $part_number = "1";
        }
        $text = self::mailboxOpenPart( $msg_number, $part_number);

        switch ($structure->encoding) {
          case(3):
            $TEXT= imap_base64($text);
            break;

          case(4):
            //return utf8_encode(urldecode(imap_qprint($structure->encoding.$text)));
            $TEXT = imap_qprint($text);
            break;

          default:
            $TEXT = $text;
            break;
        }
      }
      if($structure->type == 1) /* multipart */ {
        while(list($index, $sub_structure) = each($structure->parts)) {
          $prefix=null;
          if ($part_number) {
            $prefix = $part_number . '.';
          }
          $data = self::mailboxGetpart($msg_number, $mime_type, $sub_structure,$prefix . ($index + 1));
          if ($data) {
            return utf8_decode(imap_utf8($data));
          }
        } // END WHILE
      } // END MULTIPART
    } // END STRUTURE
    return utf8_encode(nl2br($TEXT));
  }

  /**
   * get the mime type of the structure
   *
   * @param $structure
   *
   * @return string
   */
  function get_mime_type(&$structure)
  {
    $primary_mime_type = array("TEXT", "MULTIPART","MESSAGE", "APPLICATION", "AUDIO","IMAGE", "VIDEO", "OTHER");
    if($structure->subtype) {
      return $primary_mime_type[(int) $structure->type] . '/' .$structure->subtype;
    }
    return "TEXT/PLAIN";
  }

  /**
   * return the mailbox check : new msgs
   *
   * @return object
   */
  function mailboxCheck() {
    return imap_check($this->_mailbox);
  }


  /**
   * search for a mail using specific string
   *
   * @param      $string
   * @param bool $uid
   *
   * @return array
   */
  function mailboxSearch($string,$uid=false) {
    if ($uid) {
      return imap_search($this->_mailbox,$string, SE_UID);
    }
    return imap_search($this->_mailbox, $string);
  }


  /**
   * Close the mailBox
   *
   * @return bool
   */
  function mailboxClose() {
    return imap_close($this->_mailbox);
  }

  /**
   * ReOpen a subMailbox
   *
   * @param $box
   *
   * @return bool
   */
  function mailboxOpenBox($box) {
    return imap_reopen($this->_mailbox, $this->_server.$box);
  }


}