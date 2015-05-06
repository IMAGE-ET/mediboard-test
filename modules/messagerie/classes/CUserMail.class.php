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

  public $account_id; //Source id
  public $account_class;// Source class
  //headers
  public $subject;  //subject of the mail
  public $from;  //who sent it
  public $_from;  //who sent it, readable
  public $to;  //complete recipient
  public $_to;  //recipient readable
  public $cc;
  public $_cc;
  public $bcc;
  public $_bcc;
  public $date_inbox;  //sent date
  public $date_read;  //date of the first read of the mail
  public $_msgno;  //message sequence number in the mailbox
  public $uid;
  public $answered;  //this message is flagged as answered
  public $hash; //hash of the content of the mail, the subject, the addresses

  //status
  public $favorite;  // favorite, important email
  public $archived;  // is the mail archived, (hidden)
  public $sent;      // mail has been sent
  public $draft;     // mail has been drafted

  public $in_reply_to_id; //is a reply to this message id
  public $text_file_id;
  public $_ref_file_linked;

  //body
  public $text_plain_id; //plain text (no html) = CContentAny_id
  public $_text_plain;
  public $_ref_account_;
  public $_is_apicrypt;
  public $_is_hprim;
  public $_content;

  public $text_html_id; //html text = CContentHTML_id
  public $_text_html;

  /** @var CMailAttachments[] $_attachments */
  public $_attachments           = array(); //attachments

  public $_parts;

  public $_size; //size in bytes
  public $_date_inbox;
  public $_date_read;

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
    $props["account_id"]    = "ref notNull class|CSourcePOP|CSourceSMTP meta|account_class cascade";
    $props["account_class"] = "enum list|CSourcePOP|CSourceSMTP notNull";
    $props["from"]          = "str";
    $props["_from"]         = "str";
    $props["to"]            = "str";
    $props["_to"]           = "str";
    $props['cc']            = 'str';
    $props['_cc']           = 'str';
    $props['bcc']           = 'str';
    $props['_bcc']          = 'str';
    $props["date_inbox"]    = "dateTime";
    $props["date_read"]     = "dateTime";
    $props["_msgno"]        = "num";
    $props["uid"]           = "num";
    $props["answered"]      = "bool default|0";
    $props["favorite"]      = "bool default|0";
    $props["archived"]      = "bool default|0";
    $props["sent"]          = "bool default|0";
    $props['draft']         = 'bool default|0';
    $props['hash']          = "text";
    $props['_content']      = 'html';
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

    // Remove plain content
    $content = $this->loadContentPlain();
    if ($content->_id) {
      if ($msg = $content->delete()) {
        return $msg;
      }
    }
  }

  public function updateFormFields() {
    $this->_date_inbox = CMbDT::date(null, $this->date_inbox);
    if ($this->_date_inbox == CMbDT::date()) {
      $this->_date_inbox = CMbDT::format($this->date_inbox, '%H:%M');
    }
    else if (CMbDT::format($this->date_inbox, '%Y') == CMbDT::format(CMbDT::date(), '%Y')) {
      $this->_date_inbox = CMbDT::format($this->date_inbox, '%d %B');
    }

    $this->_date_read = CMbDT::date(null, $this->date_read);
    if ($this->_date_read == CMbDT::date()) {
      $this->_date_read = CMbDT::format($this->date_read, '%H:%M');
    }
    else if (CMbDT::format($this->date_read, '%Y') == CMbDT::format(CMbDT::date(), '%Y')) {
      $this->_date_read = CMbDT::format($this->date_read, '%d %B');
    }
  }

  /**
   * return the list of uid for an account_id
   *
   * @param int $account_id account id = source_pop_id
   *
   *
   * @return array
   */
  static function getListMailInMb($account_id) {
    $mail = new self;
    $ds = $mail->getDS();
    $query = "SELECT `uid` FROM `user_mail` WHERE `account_id` = '$account_id' AND `account_class` = 'CSourcePOP'";
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
    $query = "SELECT MAX(`uid`) FROM `user_mail` WHERE `account_id` = '$account_id' AND `account_class` = 'CSourcePOP'";
    return $ds->loadResult($query);
  }

  static function getLastMailDate($account_id) {
    $mail = new self;
    $ds = $mail->getDS();
    $query = "SELECT MAX(`date_inbox`) FROM `user_mail` WHERE `account_id` = '$account_id' AND `account_class` = 'CSourcePOP'";
    $date = $ds->loadResult($query);
    $date = ($date) ? $date : CMbDT::dateTime();
    return $date;
  }

  static function getFirstMailDate($account_id) {
    $mail = new self;
    $ds = $mail->getDS();
    $query = "SELECT MIN(`date_inbox`) FROM `user_mail` WHERE `account_id` = '$account_id' AND `account_class` = 'CSourcePOP'";
    $date = $ds->loadResult($query);
    $date = ($date) ? $date : CMbDT::dateTime();
    return $date;
  }


  /**
   * Used to load the mail from SourcePOP
   *
   * @param string $hash The hash of the mail
   *
   * @return bool|int|null
   */
  function loadMatchingFromHash($hash) {
    $this->hash = $hash;
    $this->loadMatchingObject();

    return $this->_id;
  }

  public function setHeaderFromSource($source) {
    //assignment
    $this->uid          = $source->uid;
    $this->loadMatchingObject();

    $this->subject      = (isset($source->subject)) ? self::flatMimeDecode($source->subject) : null;
    $this->from         = (isset($source->fromaddress)) ? self::flatMimeDecode($source->fromaddress) : null;
    $this->to           = (isset($source->toaddress)) ? self::flatMimeDecode($source->toaddress) : null;
    $this->cc           = (isset($source->ccaddress)) ? self::flatMimeDecode($source->ccaddress) : null;
    $this->bcc          = (isset($source->bccaddress)) ? self::flatMimeDecode($source->bccaddress) : null;
    $this->date_inbox   = (isset($source->date)) ? CMbDT::dateTime($source->date) : CMbDT::dateTime();

    //cleanup
    if (empty($source->Unseen)) {
      $this->date_read = $this->date_inbox;
    }

    $this->unescapeValues();
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

      //apicrypt
      if (CModule::getActive("apicrypt") && $this->_is_apicrypt == "html") {
        $this->_text_html = CApicrypt::uncryptBody($source_object_id, $this->_text_html);
      }

      $textH->content = CUserMail::purifyHTML($this->_text_html); //cleanup

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


          //apicrypt attachment
          if (strpos($_attch->name, ".apz") !== false) {
            $file_pop = CApicrypt::uncryptAttachment($popClient->source->object_id, $file_pop);
          }

          //file type detection
          $first = (is_array($file_pop)) ? reset($file_pop) : $file_pop;
          $mime = $this->extensionDetection($first);

          //file name
          $infos = pathinfo($_attch->name);
          $extension = $infos['extension'];
          $mime_extension = strtolower(end(explode("/", $mime)));
          if (strtolower($extension) != $mime_extension) {
            $file->file_name  = $infos['filename'].".".$mime_extension;
          }

          $file->file_type  = $mime ? $mime : $_attch->getType($_attch->type, $_attch->subtype);
          $file->fillFields();
          $file->updateFormFields();
          $file->putContent($file_pop);
          $file->store();
        }
      }
    }
  }

  function extensionDetection($file_contents) {
    $dir = dirname(dirname(dirname(dirname(__FILE__)))) . "/tmp/attachment";
    file_put_contents($dir, $file_contents);
    $mime = mime_content_type($dir);
    unset($dir);
    return $mime;
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
  function setContentFromSource($contentsource) {
    $this->_text_plain   = $contentsource["text"]["plain"];
    $this->_is_apicrypt  = $contentsource["text"]["is_apicrypt"];
    $this->_text_html    = $contentsource["text"]["html"];
    $this->_attachments  = $contentsource["attachments"];
    return;
  }

  /**
   * Make the hash for the given headers and mail content
   *
   * @param $header The headers, returned by the POP source
   * @param $content The content, returned by the POP source
   *
   * @return bool|string
   */
  public function makeHash($header, $content) {
    if (isset($header->fromaddress) && isset($header->toaddress) && isset($header->subject)) {
      return null;
    }

    $data = "==FROM==\n" . self::flatMimeDecode($header->fromaddress) .
      "\n==TO==\n" . self::flatMimeDecode($header->toaddress) .
      "\n==SUBJECT==\n" . self::flatMimeDecode($header->subject);

    if (!empty($content['text']['html'])) {
      $content = $content['text']['html'];
    }
    elseif (!empty($content['text']['plain'])) {
      $content = $content['text']['plain'];
    }

    $data .= "\n==CONTENT==\n$content";
    return CMbSecurity::hash(CMbSecurity::SHA256, $data);
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
    if (strpos($string, 'UTF-8') !== false) {
      $str = utf8_decode($str);
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
      $_attachment->loadFiles();
      if (!isset($_attachment->_id) || $_attachment->disposition != "INLINE") {
        continue;
      }

      $_attachment->id = preg_replace("/(<|>)/", "", $_attachment->id);
      if (preg_match("/$_attachment->id/", $this->_text_html->content)) {
        if (isset($_attachment->_file->_id)) {
          $url = "?m=files&a=fileviewer&suppressHeaders=1&file_id=".$_attachment->_file->_id;
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
    return $this->_ref_source_account = CMbObject::loadFromGuid("$this->account_class-$this->account_id", true);
  }

  /**
   * load attachments of the present mail
   *
   * @return CStoredObject[]
   */
  function loadAttachments() {
    return  $this->_attachments = $this->loadBackRefs("mail_attachments", 'part ASC');
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

    if (stripos($this->_text_plain->content, "**FIN**") !== false) {
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

  /**
   * Count the unread mails for an account
   *
   * @param int $account_id The account id
   *
   * @return int
   */
  public static function countUnread($account_id) {
    $where['account_id'] = "= '$account_id'";
    $where['account_class'] = "= 'CSourcePOP'";
    $where['archived'] = "= '0'";
    $where['sent'] = "= '0'";
    $where['date_read'] = 'IS NULL';
    $where['draft'] = "= '0'";

    $mail = new CUserMail();
    return $mail->countList($where);
  }

  /**
   * Count the mails in the inbox for an account
   *
   * @param int $account_id The account id
   *
   * @return int
   */
  public static function countInbox($account_id) {
    $where['account_id'] = "= '$account_id'";
    $where['account_class'] = "= 'CSourcePOP'";
    $where['archived'] = "= '0'";
    $where['sent'] = "= '0'";
    $where['draft'] = "= '0'";

    $mail = new CUserMail();
    return $mail->countList($where);
  }

  /**
   * Load the mails in the inbox for an account
   *
   * @param int $account_id The account id
   * @param int $start      The start
   * @param int $limit      The number of mails to load
   *
   * @return CUserMail[]
   */
  public static function loadInbox($account_id, $start, $limit) {
    $where['account_id'] = "= '$account_id'";
    $where['account_class'] = "= 'CSourcePOP'";
    $where['archived'] = "= '0'";
    $where['sent'] = "= '0'";
    $where['draft'] = "= '0'";

    $order = "date_inbox DESC";
    $limit= "$start, $limit";
    $mail = new CUserMail();
    return $mail->loadList($where, $order, $limit);
  }

  /**
   * Count the archived mails for an account
   *
   * @param int $account_id The account id
   *
   * @return int
   */
  public static function countArchived($account_id) {
    $where['account_id'] = "= '$account_id'";
    $where['account_class'] = "= 'CSourcePOP'";
    $where['archived'] = "= '1' ";

    $mail = new CUserMail();
    return $mail->countList($where);
  }

  /**
   * Load the archived mails for an account
   *
   * @param int $account_id The account id
   * @param int $start      The start
   * @param int $limit      The number of mails to load
   *
   * @return CUserMail[]
   */
  public static function loadArchived($account_id, $start, $limit) {
    $where['account_id'] = "= '$account_id'";
    $where['account_class'] = "= 'CSourcePOP'";
    $where['archived'] = "= '1' ";

    $order = "date_inbox DESC";
    $limit= "$start, $limit";
    $mail = new CUserMail();
    return $mail->loadList($where, $order, $limit);
  }

  /**
   * Count the favoured mails for an account
   *
   * @param int $account_id The account id
   *
   * @return int
   */
  public static function countFavorites($account_id) {
    $where['account_id'] = "= '$account_id'";
    $where['account_class'] = "= 'CSourcePOP'";
    $where['favorite'] = "= '1' ";

    $mail = new CUserMail();
    return $mail->countList($where);
  }

  /**
   * Load the favoured mails for an account
   *
   * @param int $account_id The account id
   * @param int $start      The start
   * @param int $limit      The number of mails to load
   *
   * @return CUserMail[]
   */
  public static function loadFavorites($account_id, $start, $limit) {
    $where['account_id'] = "= '$account_id'";
    $where['account_class'] = "= 'CSourcePOP'";
    $where['favorite'] = "= '1' ";

    $order = "date_inbox DESC";
    $limit= "$start, $limit";
    $mail = new CUserMail();
    return $mail->loadList($where, $order, $limit);
  }

  /**
   * Count the number of sent mails for an account
   *
   * @param int $account_id The account id
   *
   * @return int
   */
  public static function countSent($account_id) {
    $source_smtp = CExchangeSource::get('mediuser-' . CMediusers::get()->_id, "smtp");
    if ($source_smtp->_id) {
      $where[] = "(account_id = '$account_id' AND account_class = 'CSourcePOP') OR (account_id = '$source_smtp->_id' AND account_class = 'CSourceSMTP')";
    }
    else {
      $where['account_id'] = "= '$account_id'";
      $where['account_class'] = "= 'CSourcePOP'";
    }
    $where['sent'] = " = '1' ";

    $mail = new CUserMail();
    return $mail->countList($where);
  }

  /**
   * Load the sent mails for an account
   *
   * @param int $account_id The account id
   * @param int $start      The start
   * @param int $limit      The number of mails to load
   *
   * @return CUserMail[]
   */
  public static function loadSent($account_id, $start, $limit) {
    $source_smtp = CExchangeSource::get('mediuser-' . CMediusers::get()->_id, "smtp");
    if ($source_smtp->_id) {
      $where[] = "(account_id = '$account_id' AND account_class = 'CSourcePOP') OR (account_id = '$source_smtp->_id' AND account_class = 'CSourceSMTP')";
    }
    else {
      $where['account_id'] = "= '$account_id'";
      $where['account_class'] = "= 'CSourcePOP'";
    }
    $where['sent'] = " = '1' ";

    $order = "date_inbox DESC";
    $limit= "$start, $limit";
    $mail = new CUserMail();
    return $mail->loadList($where, $order, $limit);
  }

  /**
   * Count the number of drafted mails for an account
   *
   * @param int $account_id The account id
   *
   * @return int
   */
  public static function countDrafted($account_id) {
    $where['account_id'] = "= '$account_id'";
    $where['account_class'] = "= 'CSourcePOP'";
    $where['draft'] = "= '1' ";

    $mail = new CUserMail();
    return $mail->countList($where);
  }

  /**
   * Load the drafted mails for an account
   *
   * @param int $account_id The account id
   * @param int $start      The start
   * @param int $limit      The number of mails to load
   *
   * @return CUserMail[]
   */
  public static function loadDrafted($account_id, $start, $limit) {
    $where['account_id'] = "= '$account_id'";
    $where['account_class'] = "= 'CSourcePOP'";
    $where['draft'] = "= '1' ";

    $order = "date_inbox DESC";
    $limit= "$start, $limit";
    $mail = new CUserMail();
    return $mail->loadList($where, $order, $limit);
  }

  /**
   * Purify a HTML string without deleting the embedded image
   *
   * @param string $html The HTML code to purify
   *
   * @return string
   */
  public static function purifyHTML($html) {
    $matches = array();
    $embedded_images = array();
    /* We replace the img tags by div tags,
     * because HTMLPurifier remove the img tag of the embedded images
     */
    if (preg_match_all('#<img[^>]*>#i', $html, $matches)) {
      foreach ($matches[0] as $_key => $_img) {
        $embedded_images[$_key] = $_img;
        /* We close the unclosed img tags */
        if (strpos($_img, '/>') === false){
          $embedded_images[$_key] = str_replace('>', '/>', $_img);
        }
        $html = str_replace($_img, "<div class=\"image-$_key\"></div>", $html);
      }
    }
    $html = CMbString::purifyHTML($html);

    $search = array();
    /* The div tags are  replaced by the img tags*/
    foreach ($embedded_images as $index => $img) {
      $search[$index] = "<div class=\"image-$index\"></div>";
    }
    return str_replace($search, $embedded_images, $html);
  }
}