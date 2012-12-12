<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */


/**
 * Used for external e-mail from the CsourcePOP
 */
class CUserMail extends CMbObject{

  var $user_mail_id = null;  //key

  //headers
  var $subject      =null;  //subject of the mail
  var $from         =null;  //who sent it
  var $to           =null;  //complete recipient
  var $_to          =null;  //recipient readable
  var $date         =null;  //sent date
  var $message_id   =null;  //Message-ID
  var $msgno        =null;  //message sequence number in the mailbox
  var $recent       =null;  //this message is flagged as recent
  var $flagged      =null;  //this message is flagged
  var $answered     =null;  //this message is flagged as answered
  var $deleted      =null;  //this message is flagged for deletion
  var $seen         =null;  //this message is flagged as already read
  var $draft        =null;  //this message is flagged as being a draft

  var $update       =null;  //timestamp update

  var $references   =null; //is a reference to this message id
  var $in_reply_to  =null; //is a reply to this message id

  //body
  var $type         =null;  //Primary body type
  var $_encoding    =null;  //Body transfer encoding
  var $ifsubtype    =null;  //TRUE if there is a subtype string
  var $subtype 	    =null;  //MIME subtype
  var $parts        =null;




  var $content      =null;

  var $_size        =null; //size in bytes


  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'user_mail';
    $spec->key   = 'user_mail_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["subject"]       = "str";
    $props["from"]          = "str";
    $props["to"]            = "str";
    $props["_to"]            = "str";
    $props["date"]          = "dateTime";
    $props["answered"]      = "bool";
    $props["content"]       = "html";
    return $props;
  }

  /**
   * used to load the mail from SourcePOP
   *
   * @param $s : stdout from IMAP
   */

  function loadHeaderFromSource($s) {

    if(!count($s)>0 || !isset($s[0]->to)) {
      return false;
    }
    $s = $s[0];

    if (isset($s->subject)) {
      $this->subject      = self::flatMimeDecode($s->subject);
    }
    $this->from         = self::flatMimeDecode($s->from);
    $this->_from        = self::adressToUser($this->from);
    $this->to           = self::flatMimeDecode($s->to);
    $this->_to          = self::adressToUser($this->to);
    $this->date         = strtotime($s->date);
    $this->_size        = $s->size;
    $this->msgno        = $s->msgno;

    return true;
  }

  /**
   * load mail content from CcoursePOP source
   *
   * @param $contentsource
   */
  function loadContentFromSource($contentsource) {
      $this->content      = $contentsource;
  }

  /**
   * Load Complete email
   *
   * @param $header
   * @param $content
   */
  function loadMail($header,$content){
    self::loadHeaderFromSource($header);
    self::loadContentFromSource($content);
    return $this;
  }

  /**
   * used for decoding a multi mime string into one line
   *
   * @param $string
   *
   * @return string
   */
  private function flatMimeDecode($string) {
    $array = imap_mime_header_decode($string);
    $str = "";
    foreach ($array as $key => $part) {
      $str .= $part->text;
    }
    return @$str;
  }

  /**
   * used for show the cleaned from string
   *
   * @param $string
   */
  private function adressToUser($string) {
    $email_complex = '/^(.+)(<[A-Za-z0-9._%-@ +]+>)$/';
    if (preg_match($email_complex,$string,$out)) {
      if (count($out)>1) {
        $out = str_replace('"', "", $out);
        return $out[1];
      }
    }
    return $string;
  }






}