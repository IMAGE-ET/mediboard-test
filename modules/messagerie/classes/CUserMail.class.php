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

  var $account_id     = null; //POP account
  //headers
  var $subject        = null;  //subject of the mail
  var $from           = null;  //who sent it
  var $_from          = null; //who sent it, readable
  var $to             = null;  //complete recipient
  var $_to            = null;  //recipient readable
  var $date_inbox     = null;  //sent date
  var $date_read      = null;  //date of the first read of the mail
  var $_msgno         = null;  //message sequence number in the mailbox
  var $uid            = null;
  var $answered       = null;  //this message is flagged as answered

  //var $msg_references = null; //is a reference to this message id
  var $in_reply_to_id      = null; //is a reply to this message id
  var $text_file_id = null;
  var $_ref_file_linked = null;

  //body
  var $text_plain_id     = null; //plain text (no html)
  var $_text_plain      = null;
  var $_account_pop     = null;

  var $text_html_id      = null; //html text
  var $_text_html       = null;

  var $_attachments     = array(); //attachments

  var $_parts         = null;

  var $_size          = null; //size in bytes

  /**
   * get specs
   *
   * @return CMbObjectSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'user_mail';
    $spec->key   = 'user_mail_id';
    return $spec;
  }

  /**
   * get Props
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["subject"]       = "str";
    $props["account_id"]    = "ref notNull class|CSourcePOP cascade";
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
    $props["text_file_id"] = "ref class|CFile";

    $props["text_plain_id"]    = "ref class|CContentAny show|0";
    $props["text_html_id"]     = "ref class|CContentHTML show|0";

    return $props;
  }


  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["mail_attachment"]            = "CMailAttachments mail_id";
    return $backProps;
  }

  /**
   * delete (delete html & text
   *
   * @return null|string
   */
  function delete() {
    $this->loadRefsFwd();

    if ($msg = parent::delete()) {
      return $msg;
    }

    // Remove html content
    if ($this->_text_html->_id) {
      if ($msg = $this->_text_html->delete()) {
        return $msg;
      }
    }

    //remove text content
    if ($this->_text_plain->_id) {
      if ($msg = $this->_text_plain->delete()) {
        return $msg;
      }
    }
  }

  /**
   * used to load the mail from SourcePOP
   *
   * @param array|object $s object from imap structure
   *
   * @return bool|int|null
   */
  function loadMatchingFromSource($s) {

    if (!count($s)>0 || !isset($s[0]->to)) {
      return false;
    }
    $s = $s[0];

    if (isset($s->subject)) {
      $this->subject      = self::flatMimeDecode($s->subject);
    }

    $this->from         = self::flatMimeDecode($s->from);
    $this->to           = self::flatMimeDecode($s->to);
    $this->date_inbox   = mbDateTime($s->date);
    $this->uid          = $s->uid;

    $this->loadMatchingObject();
    $this->unescapeValues();
    return $this->_id;
  }

  /**
   * load the visual fields
   *
   * @return null
   */
  function loadComplete() {
    $this->_from = $this->adressToUser($this->from);
    $this->_to   = $this->adressToUser($this->to);
    return;
  }

  /**
   * load mail content from CcoursePOP source
   *
   * @param array $contentsource test
   *
   * @return null
   */
  function loadContentFromSource($contentsource) {
    $this->_text_plain   = $contentsource["text"]["plain"];
    $this->_text_html    = $contentsource["text"]["html"];
    $this->_attachments  = $contentsource["attachments"];
    return;
  }

  /**
   * Load Complete email
   *
   * @param object $header  test
   * @param array  $content test
   *
   * @return CUserMail
   */
  function loadMail($header,$content){
    self::loadMatchingFromSource($header);
    self::loadContentFromSource($content);
    return $this;
  }

  /**
   * used for decoding a multi mime string into one line
   *
   * @param string $string decode mime string
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

  /**
   * check if html content has image inline and return true if an image has been found.
   *
   * @return bool
   */
  function checkInlineAttachments() {
    if (!count($this->_attachments) || !$this->_text_html->content) {
      return false;
    }

    foreach ($this->_attachments as $_attachment) {
      $_attachment->loadRefsFwd();
      if (isset($_attachment->_id) && $_attachment->disposition == "INLINE") {
        $_attachment->id = preg_replace("/(<|>)/", "", $_attachment->id);
        if (preg_match("/$_attachment->id/", $this->_text_html->content)) {
          if (isset($_attachment->_file->_id)) {
            $url = "?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id=".$_attachment->_file->_id. "&amp;phpThumb=1&amp;f=png";
            $this->_text_html->content = str_replace("cid:$_attachment->id", $url , $this->_text_html->content);
          }
        }
      }
    }
    return true;
  }

  /**
   * return the cleaned string
   *
   * @param string $string an address string example: <foo@bar.com>"Mr Foo"
   *
   * @return mixed
   */
  private function adressToUser($string) {
    $email_complex = '/^(.+)(<[A-Za-z0-9._%-@ +]+>)$/';
    if (preg_match($email_complex, $string, $out)) {
      if (count($out)>1) {
        $out = str_replace('"', "", $out);
        return $out[1];
      }
    }
    return $string;
  }

  /**
   * load the text_plain ref
   *
   * @return CMbObject
   */
  function loadContentPlain() {
    return $this->_text_plain = $this->loadFwdRef("text_plain_id");
  }

  /**
   * load the text_html ref
   *
   * @return CSourceHTML
   */
  function loadContentHTML() {
    return $this->_text_html = $this->loadFwdRef("text_html_id");
  }

  function loadAccount() {
    return $this->_account_pop = $this->loadFwdRef("account_id");
  }

  function loadAttachments() {
    $attach = new CMailAttachments();
    $attach->mail_id = $this->_id;
    $attachs = $attach->loadMatchingList();
    return $this->_attachments = $attachs;
  }

  function loadFileLinked() {
    $file = new CFile();
    $file->load($this->text_file_id);
    $file->loadRefsFwd();
    return $this->_ref_file_linked = $file;
  }

  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadContentHTML();
    $this->loadContentPlain();
    $this->loadAttachments();
    $this->loadAccount();
    $this->loadFileLinked();
  }

}