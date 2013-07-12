<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */


/**
 * Description : Attachment of an email
 **/
class CMailAttachments extends CMbObject{

  public $user_mail_attachment_id;

  public $mail_id;

  public $type;
  public $encoding;
  public $subtype;
  public $id;
  public $bytes;
  public $disposition;
  public $part;
  public $file_id;        //Cfile id if linked

  public $name;
  public $extension;


  /** @var CFile|null $_file */
  public $_file;
  public $_content;     // temp for content of file
  public $_ref_mail;    // for mail ref


  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'user_mail_attachment';
    $spec->key   = 'user_mail_attachment_id';
    $spec->loggable = false;
    return $spec;
  }

  /**
   *  @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["mail_id"]       = "ref notNull class|CUserMail cascade";
    $props["type"]          = "num notNull";
    $props["encoding"]      = "num";
    $props["subtype"]       = "str";
    $props["id"]            = "str";
    $props["bytes"]         = "num";
    $props["disposition"]   = "str";
    $props["part"]          = "str notNull";
    $props["file_id"]       = "ref class|CFile";

    $props["name"]          = "str notNull";
    $props["extension"]     = "str notNull";

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backprops = parent::getBackProps();
    $backprops["files"] = "CFile object_id cascade";
    return $backprops;
  }
  /**
   * Load MailAttachment from POP Header
   *
   * @param object $header header from source POP
   *
   * @return CMAilAttachments $this
   */
  function loadFromHeader($header) {
    $this->type = $header->type;
    $this->encoding = $header->encoding;
    if ($header->ifsubtype) {
      $this->subtype = $header->subtype;
    }
    if ($header->ifid) {
      $this->id = $header->id;
    }
    $this->bytes = $header->bytes;
    if ($header->ifdisposition) {
      $this->disposition = $header->disposition;
    }
    else { //nothing for disposition ?
      $this->disposition = 'INLINE';
    }
    if ($header->ifdparameters) {
      $this->name = $header->dparameters[0]->value;
    }
    if ($header->ifparameters) {
      $this->name = $header->parameters[0]->value;
    }

    //extension
    if ($ext = substr(strrchr($this->name, '.'), 1)) {
      $this->extension = $ext;
    }
    $this->name = addslashes($this->name);

    return $this;
  }

  /**
   * LoadContent from pop content
   *
   * @param object $content content from pop
   *
   * @return bool
   */
  function loadContentFromPop($content) {
    switch ($this->subtype) {
      case 'SVG+XML':
        return $this->_content = CPop::decodeMail($this->encoding, $content);
        break;

      default:
        return $this->_content = base64_encode(CPop::decodeMail($this->encoding, $content));
        break;
    }
  }

  /**
   * Get the string $this->part++
   *
   * @return string $ret
   */
  function getpartDL() {
    $ret = "";
    $parts = explode(".", $this->part);
    if (count($parts)>1) {
      foreach ($parts as $key=>$_part) {
        $ret.=$_part+1;
        if ($key+1 != count($parts)) {
          $ret.= '.';
        }
      }
    }
    else {
      $ret = $this->part+1;
    }
    return $ret;
  }

  /**
   * Load the forward refs
   *
   * @return int|void
   */
  function loadRefsFwd() {
    $this->loadFiles();
  }

  /**
   * load the mail where this attachment is attached
   *
   * @return int|void
   */
  function loadMail() {
    return $this->_ref_mail = $this->loadRefsFwd("mail_id");
  }

  /**
   * load files linked to the present attachment
   *
   * @return CFile
   */
  function loadFiles() {
    //a file is already linked and we have the id
    if ($this->file_id) {
      $file = new CFile();
      $file->load($this->file_id);
      $file->loadRefsFwd();         //TODO : fix this
      $file->updateFormFields();
    }
    //so there is a file linked
    else {
      $file = new CFile();
      $file->setObject($this);
      $file->loadMatchingObject();
      //$file->loadUniqueBackRef("files");
      $file->updateFormFields();
    }

    return $this->_file = $file;
  }

  /**
   * get attachment mime type
   *
   * @param int    $type      type from 0 to 6
   * @param string $extension extension (png, jpg ...)
   *
   * @return string
   */
  function getType($type,$extension) {
    switch ($type) {
      case 0:
        $string ='text';
        break;

      case 1:
        $string ='multipart';
        break;

      case 2:
        $string = 'message';
        break;

      case 3:
        $string = 'application';
        break;

      case 4:
        $string = 'audio';
        break;

      case 5:
        $string = 'image';
        break;

      case 6:
        $string = 'video';
        break;

      default:
        $string = 'other';
        break;
    }
    return strtolower($string.'/'.$extension);
  }
}