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

  var $user_mail_attachment_id = null;

  var $mail_id      = null;

  var $type         = null;
  var $encoding     = null;
  var $subtype      = null;
  var $id           = null;
  var $bytes        = null;
  var $disposition  = null;
  var $part         = null;
  var $linked       = null;

  var $name         = null;
  var $extension    = null;


  var $_file        = null;
  var $_content      = null; //temp for content of file


  /**
   * get Spec
   *
   * @return CMbObjectSpec CMbObjectSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'user_mail_attachment';
    $spec->key   = 'user_mail_attachment_id';
    return $spec;
  }

  /**
   *  get Props
   *
   * @return array props
   */
  function getProps() {
    $props = parent::getProps();
    $props["mail_id"]       = "ref notNull class|CUserMail";
    $props["type"]          = "num notNull";
    $props["encoding"]      = "num";
    $props["subtype"]       = "str";
    $props["id"]            = "str";
    $props["bytes"]         = "num";
    $props["disposition"]   = "str";
    $props["part"]          = "str notNull";
    $props["linked"]        = "str";

    $props["name"]          = "str notNull";
    $props["extension"]     = "str notNull";

    return $props;
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
        $this->_content = CPop::decodeMail($this->encoding, $content);
        break;

      default:
        $this->_content = base64_encode(CPop::decodeMail($this->encoding, $content));
        break;
    }
    return true;
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
   * load files linked to the present attachment
   *
   * @return CFile
   */
  function loadFiles() {
    $file = new CFile();
    $file->object_class = 'CMailAttachments';
    $file->object_id = $this->_id;
    $file->loadMatchingObject();
    $file->updateFormFields();
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