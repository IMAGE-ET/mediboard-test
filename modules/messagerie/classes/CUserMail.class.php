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

  var $user_id      = null; //user_id
  //headers
  var $subject        = null;  //subject of the mail
  var $from           = null;  //who sent it
  var $_from           = null; //who sent it, readable
  var $to             = null;  //complete recipient
  var $_to            = null;  //recipient readable
  var $date_inbox     = null;  //sent date
  var $date_read      = null;  //date of the first read of the mail
  var $_msgno         = null;  //message sequence number in the mailbox
  var $uid            = null;
  var $answered       = null;  //this message is flagged as answered

  //var $msg_references = null; //is a reference to this message id
  var $in_reply_to_id    = null; //is a reply to this message id

  //body
  var $text_plain_id     = null; //plain text (no html)
  var $_text_plain      = null;

  var $text_html_id      = null; //html text
  var $_text_html       = null;

  var $_attachments     = null; //attachments

  var $_parts         = null;

  var $_size          = null; //size in bytes


  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'user_mail';
    $spec->key   = 'user_mail_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["subject"]       = "str";
    $props["user_id"]       = "ref notNull class|CMediusers";
    $props["from"]          = "str";
    $props["_from"]         = "str";
    $props["to"]            = "str";
    $props["_to"]           = "str";
    $props["date_inbox"]    = "dateTime";
    $props["date_read"]     = "dateTime";
    $props["_msgno"]        = "num";
    $props["uid"]           = "num";
    $props["answered"]      = "bool default|0";
    //$props["msg_references"]= "str";
    $props["in_reply_to_id"]   = "ref class|CUserMail";

    $props["text_plain_id"]    = "ref class|CContentAny show|0";
    $props["text_html_id"]     = "ref class|CContentHTML show|0";

    return $props;
  }

  /**
   * @return null|string
   */
  function delete() {
    $this->loadRefsFwd();

    if ($msg = parent::delete()) {
      return $msg;
    }
    // Remove content
    if ($this->_text_html->_id) {
      if ($msg = $this->_text_html->delete()) {
        return $msg;
      }
    }

    if ($this->_text_plain->_id) {
      if($this->_text_plain->delete()) {
        return $msg;
      }
    }
  }

  /**
   * used to load the mail from SourcePOP
   *
   * @param $s : stdout from IMAP
   */

  function loadMatchingFromSource($s) {

    if(!count($s)>0 || !isset($s[0]->to)) {
      return false;
    }
    $s = $s[0];

    if (isset($s->subject)) {
      $this->subject      = self::flatMimeDecode($s->subject);
    }

    $this->from         = self::flatMimeDecode($s->from);
    $this->to           = self::flatMimeDecode($s->to);
    $this->date_inbox   = mbDateTime($s->date);
    //$this->_msgno       = $s->msgno;
    $this->uid          = $s->uid;

    $this->loadMatchingObject();
    $this->unescapeValues();
    return $this->_id;
  }

  function loadComplete() {
    $this->_from = $this->adressToUser($this->from);
    $this->_to   = $this->adressToUser($this->to);
  }

  /**
   * load mail content from CcoursePOP source
   *
   * @param $contentsource
   */
  function loadContentFromSource($contentsource) {
    $this->_text_plain   = $contentsource["text"]["plain"];
    $this->_text_html    = $contentsource["text"]["html"];
    $this->_attachments  = $contentsource["attachments"];
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
    return addslashes($str);
  }


  function checkInlineAttachments() {
    if (!count($this->_attachments) || !$this->_text_html->content) {
      return false;
    }

    foreach($this->_attachments as $_attachment) {
      if (isset($_attachment->id) && $_attachment->disposition == "INLINE") {
        $_attachment->id = preg_replace("/(<|>)/", "", $_attachment->id);
        if (preg_match("/$_attachment->id/",$this->_text_html->content)) {
          $this->_text_html->content = str_replace("cid:$_attachment->id","data:image/".strtolower($_attachment->subtype).";base64,".$_attachment->content,$this->_text_html->content);
        }
      }
    }
    return true;
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

  function loadContentPlain() {
    return $this->_text_plain = $this->loadFwdRef("text_plain_id");
  }

  function loadContentHTML() {
    return $this->_text_html = $this->loadFwdRef("text_html_id");
  }

  function loadAttachments() {
    $attach = new CMailAttachments();
    $attach->mail_id = $this->_id;
    $attachs = $attach->loadMatchingList();
    return $this->_attachments = $attachs;
  }

  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadContentHTML();
    $this->loadContentPlain();
    $this->loadAttachments();
  }

}