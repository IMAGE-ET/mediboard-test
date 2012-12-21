<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */

/**
 * Description
 */
class CPop{

  var $source         = null;

  var $_mailbox       = null; //ressource id
  var $_server        = null;
  var $_mailbox_info  = null;

  var $content = array (
    "text" => array(
      "plain" => null,
      "html"  => null
    ),
    "attachments" => array()
  );


  /**
   * constructor
   * INIT function : Initialisation of the remote connexion to IMAP/POP
   *
   * @return string
   */
  function CPop($source) {
    //stock the source
    $this->source = $source;

    if (!function_exists("imap_open")) {
      CAppUI::stepAjax("CPop-error-no_imap_lib", UI_MSG_ERROR);
    }

    //initialise open TIMEOUT
    imap_timeout( 1, $this->source->timeout );


    //lets create the string for stream
    $type = ($this->source->type == "pop3")?"/".$this->source->type:"";
    $ssl  = ($this->source->auth_ssl == "SSL/TLS")?"/ssl/novalidate-cert":"";
    return $this->_server = "{".$this->source->host.":".$this->source->port.$type.$ssl."}";
  }

  /**
   * Open the remote mailbox
   *
   * @return ressource
   */
  function open() {

    if(!isset($this->_server)) {
      CAppUI::stepAjax("CPop-error-notInitiated",UI_MSG_ERROR);
    }


    $this->_mailbox = @imap_open($this->_server, $this->source->user, $this->source->password,0, 0);
    if($this->_mailbox === false ) {
      CAppUI::stepAjax("CPop-error-imap_open", UI_MSG_ERROR);
    }
    //get the basics
    $this->_mailbox_info = imap_check($this->_mailbox);
    return $this->_mailbox;

  }

  /**
   * return the mailbox check : new msgs
   *
   * @return object
   */
  function check() {
      $this->_mailbox_info = imap_check($this->_mailbox);
    return $this->_mailbox_info;
  }


  /**
   * search for a mail using specific string
   *
   * @param      $string
   * @param bool $uid
   *
   * @return array
   */
  function search($string,$uid=true) {
    if(!is_string($string)) {
      CAppUI::stepAjax("CPop-error-search-notString",UI_MSG_ERROR);
    }
    if ($uid) {
      return imap_sort($this->_mailbox,SORTDATE,1,SE_UID,$string);
    }
    return imap_sort($this->_mailbox,SORTDATE,1,0,$string);
  }

  /**
   * get the header of the mail
   *
   * @param $id
   *
   * @return array
   */
  function header($id) {
    return imap_fetch_overview($this->_mailbox, $id,FT_UID);
  }

  /**
   * get the structure of the mail (parts)
   * @param $id
   *
   * @return object
   */
  function structure($id) {
    return imap_fetchstructure($this->_mailbox, $id, FT_UID);
  }


  /**
   * Open a part of an email
   * @param $msgId
   * @param $partId
   *
   * @return string
   */
  function openPart($msgId,$partId,$uid=true) {
    if ($uid) {
      return imap_fetchbody($this->_mailbox, $msgId, $partId, FT_UID);
    }

    return imap_fetchbody($this->_mailbox, $msgId, $partId);
  }

  /**
   * get the text from email
   *
   * @param $mail_id
   *
   * @return array
   */
  function getFullBody($mail_id,$structure = false, $part_number = false, $only_text = false) {

    if(!$structure) {
      $structure = $this->structure($mail_id);
    }

    if ($structure) {
      if(!isset($structure->parts) && !$part_number) {  //plain text only, no recursive
        $part_number = "1";
      }
      if(!$part_number) {
        $part_number = "0";
      }

      switch ($structure->type) {
        case 0: //text or html
          if ($structure->subtype == "PLAIN") {
            $this->content["text"]["plain"] = self::decodeMail($structure->encoding, self::openPart($mail_id,$part_number));
          }
          if ($structure->subtype == "HTML") {

            $this->content["text"]["html"] = self::decodeMail($structure->encoding, self::openPart($mail_id,$part_number));
          }

          break;
        case 1: //multipart, alternatived
          while (list($index, $sub_structure) = each($structure->parts)) {
            if ($part_number) {
              $prefix = $part_number.'.';
            } else {
              $prefix = null;
            }
            self::getFullBody($mail_id,$sub_structure,$prefix . ($index + 1));
          }
          break;

        case 2:     //message
        case 3:     //application
        case 4:     //audio
        case 5:     //images
        case 6:     //video
        default:    //other
        if ($only_text) {
          $attach  = new CPopAttachments();
          $attach->loadFromHeader($structure);
          $attach->loadContentFromPop($this->openPart($mail_id,$part_number));

          //inline attachments
          if ($attach->id && $attach->subtype!="SVG+XML") {
            $id= 'cid:'.str_replace(array("<",">"), array("",""), $attach->id);
            $this->content["text"]["html"] = str_replace($id, "data:image/$attach->subtype|strtolower;base64,".$attach->content, $this->content["text"]["html"]);
          } else {  //attachments below
            $this->content["attachments"][] = $attach;
          }
        }
      }
    }
    return $this->content;
  }



  /** TOOLS **/

  /**
   * get the right decoding string from mail structure
   * @param $encoding
   * @param $text
   *
   * @return string
   */
  function decodeMail($encoding, $text) {
    switch ($encoding) {
      /* 0 : 7 bit / 1 : 8 bit / 2 ; binary / 5 : other  => default  */
      case(3):  //base64
         return imap_base64($text);
        break;

      case(4):
        return imap_qprint($text);
        break;

      default:
        return $text;
        break;
    }
  }

  function inlineAttach() {
    
  }

  /* Close the mailBox
   *
   * @return bool
   */
  function close() {
    return imap_close($this->_mailbox);
  }


}


/* CLASS CPopATTACHMENTS */

class CPopAttachments {

  var $type         = null;
  var $encoding     = null;
  var $subtype      = null;
  var $id           = null;
  var $bytes        = null;
  var $disposition  = null;

  var $name         = null;

  var $content      = null;

  function loadFromHeader($header) {
    $this->type = $header->type;
    $this->encoding = $header->encoding;
    if ($header->ifsubtype) {$this->subtype = $header->subtype;}
    if ($header->ifid) {$this->id = $header->id;}
    $this->bytes = $header->bytes;
    if ($header->ifdisposition) {$this->disposition = $header->disposition;}
    if ($header->ifdparameters) {$this->name = $header->dparameters[0]->value;}
    if ($header->ifparameters) {$this->name = $header->parameters[0]->value;}

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
}