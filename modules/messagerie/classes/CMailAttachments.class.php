<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */


/*
 * Description : Attachment of an email
 */
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

  var $name         = null;
  var $extension    = null;

  var $_content      = null;


  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'user_mail_attachment';
    $spec->key   = 'user_mail_attachment_id';
    return $spec;
  }

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

    $props["name"]          = "str notNull";
    $props["extension"]     = "str notNull";

    return $props;
  }



  function loadFromHeader($header) {
    $this->type = $header->type;
    $this->encoding = $header->encoding;
    if ($header->ifsubtype) {$this->subtype = $header->subtype;}
    if ($header->ifid) {$this->id = $header->id;}
    $this->bytes = $header->bytes;
    if ($header->ifdisposition) {$this->disposition = $header->disposition;}
    if ($header->ifdparameters) {$this->name = $header->dparameters[0]->value;}
    if ($header->ifparameters) {$this->name = $header->parameters[0]->value;}

    //extension
    $infos = pathinfo($this->name);
    $this->extension = strtolower($infos["extension"]);
  }

  function loadContentFromPop($content) {
    switch ($this->subtype) {
      case 'SVG+XML':
        $this->content = CPop::decodeMail($this->encoding, $content);
        break;

      default:
        $this->content = base64_encode(CPop::decodeMail($this->encoding, $content));
        break;
    }
  return true;
  }

  function getpartDL() {
    $ret = "";
    $parts = explode(".",$this->part);
    if (count($parts)>1) {
      foreach ($parts as $key=>$_part) {
        $ret.=$_part+1;
        if($key+1 != count($parts)) {
          $ret.= '.';
        }
      }

    } else {
      $ret = $this->part+1;
    }
    return $ret;

  }
}