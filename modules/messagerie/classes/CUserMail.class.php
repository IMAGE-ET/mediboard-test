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

  public $user_mail_id;  //key

  public $account_id; //POP account
  //headers
  public $subject;  //subject of the mail
  public $from;  //who sent it
  public $_from;  //who sent it, readable
  public $to;  //complete recipient
  public $_to;  //recipient readable
  public $date_inbox;  //sent date
  public $date_read;  //date of the first read of the mail
  public $_msgno;  //message sequence number in the mailbox
  public $uid;
  public $answered;  //this message is flagged as answered

  //status
  public $favorite;  // favorite, important email
  public $archived;  // is the mail archived, (hidden)
  public $sent;      // mail has been sent

  public $in_reply_to_id; //is a reply to this message id
  public $text_file_id;
  public $_ref_file_linked;

  //body
  public $text_plain_id; //plain text (no html) = CContentAny_id
  public $_text_plain;
  public $_ref_account_pop;
  public $_is_apicrypt;
  public $_is_hprim;

  public $text_html_id; //html text = CContentHTML_id
  public $_text_html;

  /** @var CMailAttachments[] $_attachments */
  public $_attachments           = array(); //attachments

  public $_parts;

  public $_size; //size in bytes

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'user_mail';
    $spec->key   = 'user_mail_id';
    $spec->loggable = false;
    return $spec;
  }

  /**
   * @see parent::getProps()
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
    $props["favorite"]      = "bool default|0";
    $props["archived"]      = "bool default|0";
    $props["sent"]          = "bool default|0";
    //$props["msg_references"]= "str";
    $props["in_reply_to_id"] = "ref class|CUserMail";
    $props["text_file_id"]  = "ref class|CFile";

    $props["text_plain_id"]    = "ref class|CContentAny show|0";
    $props["text_html_id"]     = "ref class|CContentHTML show|0";

    return $props;
  }


  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["mail_attachments"]          = "CMailAttachments mail_id";
    $backProps["reply_of"]                  = "CUserMail in_reply_to_id";
    return $backProps;
  }

  /**
   * @see parent::check()
   */
  function check() {
    if ($msg = parent::check()) {
      return $msg;
    }

    //a message flag as sent cannot be archived
    if ($this->sent && $this->archived) {
      return "CUserMail-msg-AMessageSentCannotBeArchived";
    }
  }

  /**
   * @see parent::delete()
   */
  function delete() {
    if ($msg = parent::delete()) {
      return $msg;
    }

    // Remove html content
    $content = $this->loadContentHTML();
    if ($content->_id) {
      if ($msg = $content->delete()) {
        return $msg;
      }
    }

    // Remove html content
    $content = $this->loadContentPlain();
    if ($content->_id) {
      if ($msg = $content->delete()) {
        return $msg;
      }
    }
  }

  /**
   * return the list of uid for an account_id
   *
   * @param int $account_id account id = source_pop_id
   *
   * @return array
   */
  static function getListMailInMb($account_id) {
    $mail = new self;
    $ds = $mail->getDS();
    $query = "SELECT `uid` FROM `user_mail` WHERE `account_id` = '$account_id' ";
    return $ds->loadColumn($query);
  }

  /**
   * get the last uid mail from mb
   *
   * @param int $account_id account_id = source pop
   *
   * @return array|null
   */
  static function getLastMailUid($account_id) {
    $mail = new self;
    $ds = $mail->getDS();
    $query = "SELECT MAX(`uid`) FROM `user_mail` WHERE `account_id` = '$account_id' ";
    return $ds->loadResult($query);
  }

  static function getLastMailDate($account_id) {
    $mail = new self;
    $ds = $mail->getDS();
    $query = "SELECT MAX(`date_inbox`) FROM `user_mail` WHERE `account_id` = '$account_id' ";
    return $ds->loadResult($query);
  }


  /**
   * used to load the mail from SourcePOP
   *
   * @param object $source object from imap structure
   *
   * @return bool|int|null
   */
  function loadMatchingFromSource($source) {
    //assignment
    $this->uid          = $source->uid;
    $this->loadMatchingObject();

    $this->subject      = (isset($source->subject)) ? self::flatMimeDecode($source->subject) : null;
    $this->from         = self::flatMimeDecode($source->from);
    $this->to           = self::flatMimeDecode($source->to);
    $this->date_inbox   = CMbDT::dateTime($source->date);

    //cleanup
    if ($source->seen) {
      $this->date_read = $this->date_inbox;
    }

    $this->unescapeValues();
    return $this->_id;
  }


  /**
   * get the plain text from the mail structure
   *
   * @param int $source_object_id the user id
   *
   * @return mixed
   */
  function getPlainText($source_object_id) {
    if ($this->_text_plain) {
      $textP = new CContentAny();
      //apicrypt
      if (CModule::getActive("apicrypt") && $this->_is_apicrypt == "plain") {
        $textP->content = CApicrypt::uncryptBody($source_object_id, $this->_text_plain)."\n[apicrypt]";
      }
      else {
        $textP->content = $this->_text_plain;
      }

      if (!$msg = $textP->store()) {
        $this->text_plain_id = $textP->_id;
      }
    }

    return $this->text_plain_id;
  }

  /**
   * get the html text from the mail structure
   *
   * @param int $source_object_id the user id
   *
   * @return mixed
   */
  function getHtmlText($source_object_id) {
    if ($this->_text_html) {
      $textH = new CContentHTML();
      $text = CMbString::purifyHTML($this->_text_html); //cleanup
      //apicrypt
      if (CModule::getActive("apicrypt") && $this->_is_apicrypt == "html") {
        $textH->content = CApicrypt::uncryptBody($source_object_id, $text);
      }
      else {
        $textH->content = $text;
      }

      if (!$msg = $textH->store()) {
        $this->text_html_id = $textH->_id;
      }
    }

    return $this->text_html_id;
  }


  /**
   * create the CFiles attached to the mail
   *
   * @param CMailAttachments[] $attachList The list of CMailAttachment
   * @param CPop               $popClient  the CPop client
   *
   * @return void
   */
  function attachFiles($attachList, $popClient) {
    //size limit
    $size_required = CAppUI::pref("getAttachmentOnUpdate");
    if ($size_required == "") {
      $size_required = 0;
    }

    foreach ($attachList as $_attch) {
      $_attch->mail_id = $this->_id;
      $_attch->loadMatchingObject();
      if (!$_attch->_id) {
        $_attch->store();
      }
      //si preference taille ok OU que la piece jointe est incluse au texte => CFile
      if (($_attch->bytes <= $size_required ) || $_attch->disposition == "INLINE") {

        $file = new CFile();
        $file->setObject($_attch);
        $file->author_id  = CAppUI::$user->_id;

        if (!$file->loadMatchingObject()) {
          $file_pop = $popClient->decodeMail($_attch->encoding, $popClient->openPart($this->uid, $_attch->getpartDL()));
          $file->file_name  = $_attch->name;
          $file->file_type  = $_attch->getType($_attch->type, $_attch->subtype);
          $file->fillFields();
          $file->updateFormFields();
          $file->putContent($file_pop);
          $file->store();
        }
      }
    }
  }


  /**
   * load the visual fields
   *
   * @return null
   */
  function loadReadableHeader() {
    $this->_from = $this->adressToUser($this->from);
    $this->_to   = $this->adressToUser($this->to);
    return;
  }

  /**
   * load mail content from CSoursePOP source
   *
   * @param array $contentsource test
   *
   * @return null
   */
  function loadContentFromSource($contentsource) {
    $this->_text_plain   = $contentsource["text"]["plain"];
    $this->_is_apicrypt  = $contentsource["text"]["is_apicrypt"];
    $this->_text_html    = $contentsource["text"]["html"];
    $this->_attachments  = $contentsource["attachments"];
    mbLog($this);
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
    $parts = imap_mime_header_decode($string);
    $str = implode("", CMbArray::pluck($parts, "text"));
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
      $_attachment->loadFiles();
      if (!isset($_attachment->_id) || $_attachment->disposition != "INLINE") {
        continue;
      }

      $_attachment->id = preg_replace("/(<|>)/", "", $_attachment->id);
      if (preg_match("/$_attachment->id/", $this->_text_html->content)) {
        if (isset($_attachment->_file->_id)) {
          $url = "?m=files&a=fileviewer&suppressHeaders=1&file_id=".$_attachment->_file->_id. "&amp;phpThumb=1&amp;f=png";
          $this->_text_html->content = str_replace("cid:$_attachment->id", $url , $this->_text_html->content);
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
   * @return CContentAny
   */
  function loadContentPlain() {
    return $this->_text_plain = $this->loadFwdRef("text_plain_id");
  }

  /**
   * load the text_html ref
   *
   * @return CContentHTML
   */
  function loadContentHTML() {
    return $this->_text_html = $this->loadFwdRef("text_html_id");
  }

  /**
   * load accoun user
   *
   * @return CMbObject
   */
  function loadAccount() {
    return $this->_ref_account_pop = $this->loadFwdRef("account_id");
  }

  /**
   * load attachments of the present mail
   *
   * @return CStoredObject[]
   */
  function loadAttachments() {
    return  $this->_attachments = $this->loadBackRefs("mail_attachments");
  }

  /**
   * load files linked
   *
   * @return CMbObject
   */
  function loadFileLinked() {
    $file = $this->loadFwdRef("text_file_id");
    $file->loadRefsFwd(); //@TODO Fix this !
    return $this->_ref_file_linked = $file;
  }

  /**
   * check if there is hprim headers
   *
   * @return int|null
   */
  function checkHprim() {
    if ($this->_text_plain->content == "") {
      return false;
    }
    $date_regex = "^([0-3][0-9])[/](0[1-9]|1[0-2])[/]([0-9]{4})$^";
    $lines = preg_split("/(\r\n|\n)/", $this->_text_plain->content, 13);
    if (count($lines) >= 13) {
      if (preg_match($date_regex, $lines[6]) && preg_match($date_regex, $lines[9])) {
        $this->_is_hprim = 1;
      }
    }
    return $this->_is_hprim;
  }

  /**
   * Check if the content plain is from apicrypt
   *
   * @return bool|null
   */
  function checkApicrypt() {

    if ($this->_text_plain->content == "") {
      return false;
    }

    if (stripos($this->_text_plain->content, "****FIN****") !== false) {
      $this->_is_apicrypt = true;
    }
    return $this->_is_apicrypt;
  }

  /**
   * Load complete email
   *
   * @return int|void
   */
  function loadRefsFwd() {
    $this->loadContentHTML();
    $this->loadContentPlain();
    $this->loadAttachments();
    $this->loadAccount();
    $this->loadFileLinked();
    return;
  }

}