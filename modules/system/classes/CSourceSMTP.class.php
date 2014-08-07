<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CAppUI::requireLibraryFile("phpmailer/class.phpmailer");
CAppUI::requireLibraryFile("phpmailer/class.smtp");

class CSourceSMTP extends CExchangeSource {
  // DB Table key
  public $source_smtp_id;
  
  // DB Fields
  public $port;
  public $email;
  public $auth;
  public $ssl;
  public $timeout;
  public $debug;
  
  public $_mail;
  public $_to;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_smtp';
    $spec->key   = 'source_smtp_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["port"]     = "num default|25";
    $props["email"]    = "email";
    $props["auth"]     = "bool default|1";
    $props["ssl"]      = "bool";
    $props["timeout"]  = "num default|5";
    $props["debug"]    = "bool default|0";

    return $props;
  }
  
  function updatePlainFields() {
    parent::updatePlainFields();
    $this->role = "prod";
  }
  
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->email ? $this->email : $this->libelle ? $this->libelle : $this->host;
  }

  /**
   * Mailer initialisation 
   * 
   * @return void
   */
  function init() {
    $this->_mail = new PHPMailer(true);
    $this->_mail->IsSMTP();
    
    // Sets the prefix to the server
    if ($this->ssl) {
      $this->_mail->SMTPSecure = "ssl";
    }
    
    $this->_mail->Host       = $this->host;        
    $this->_mail->SMTPAuth   = $this->auth; 
    $this->_mail->Port       = $this->port;
    $this->_mail->Username   = $this->user;
    $this->_mail->Password   = $this->getPassword();
    $this->_mail->SMTPDebug  = $this->debug ? 2 : 0;
    $this->_mail->Timeout    = $this->timeout;
    
    $this->_mail->SetFrom($this->email, '', 0);
  }
  
  /**
   * Set a supposably unique to-address
   * 
   * @param string $address E-mail address
   * @param string $name    Display name
   *
   * @return bool Job done
   */
  function setRecipient($address, $name = '') {
    $this->_to = array('address' => $address, 'name' => $name);
    return $this->_mail->AddAddress($address, $name);
  }
  
  /**
   * Add a to-address
   * 
   * @param string $address E-mail address
   * @param string $name    Display name
   *
   * @return bool  Job done
   */
  function addTo($address, $name = '') {
    $this->_to = array('address' => $address, 'name' => $name);
    return $this->_mail->AddAddress($address, $name);
  }
  
  /**
   * Add a cc-address
   *
   * @param string $address E-mail address
   * @param string $name    Display name
   *
   * @return bool
   * Job done
   */
  function addCc($address, $name = '') {
    return $this->_mail->AddCC($address, $name);
  }

  /**
   * Add a bcc-address
   *
   * @param string $address E-mail address
   * @param string $name    Display name
   *
   * @return boolean Job done
   */
  function addBcc($address, $name = '') {
    return $this->_mail->AddBCC($address, $name);
  }
  
  /**
   * Add a replyto-address
   *
   * @param string $address E-mail address
   * @param string $name    Display name
   *
   * @return bool Job done
   */
  function addRe($address, $name = '') {
    return $this->_mail->AddReplyTo($address, $name);
  }
  
  function setSubject($subject) {
    $this->_mail->Subject = $subject;
  }
  
  function setBody($body) {
    $this->_mail->MsgHTML($body);
  }
  
  function addAttachment($file_path, $name='') {
    $type = mime_content_type($file_path);
    $this->_mail->AddAttachment($file_path, $name, 'base64', $type);
  }
  
  function addEmbeddedImage($file_path, $cid) {
    $this->_mail->AddEmbeddedImage($file_path, $cid);
  }
  
  function send($evenement_name = null) {
    try {
      $this->_mail->send();
    } catch(phpmailerException $e) {
      throw $e;
    }
  }

  function createUserMail($user_id, $object = null, $apicrypt = false) {
    $mail =  new CUserMail();
    $mail->account_id = $this->_id;
    $mail->account_class = $this->_class;
    $mail->sent = 1;

    $mail->subject = $this->_mail->Subject;
    $mail->from = $this->_mail->From;
    $mail->to = $this->_to['address'];
    $mail->date_inbox = CMbDT::dateTime();

    if ($this->_mail->ContentType == 'text/html') {
      $mail->_text_html = $this->_mail->Body;
      if ($apicrypt) {
        $mail->_is_apicrypt = 'html';
      }
      $mail->getHtmlText($user_id);
    }
    else {
      $mail->_text_plain = $this->_mail->Body;
      if ($apicrypt) {
        $mail->_is_apicrypt = 'plain';
      }
      $mail->getPlainText($user_id);
    }

    $mail->store();

    if ($object) {
      $file = null;
      switch ($object->_class) {
        case "CCompteRendu":
          $file = $object->_ref_file;
          break;
        case "CFile":
          $file = $object;
          break;
      }

      if ($file && $file->_id) {
        $attachment = new CMailAttachments();
        $attachment->mail_id = $mail->_id;
        list($type, $subtype) = explode('/', $file->file_type);
        $attachment->type = $attachment->getTypeInt($type);
        $attachment->part = 1;
        $attachment->subtype = $subtype;
        $attachment->bytes = $file->file_size;
        list($file_name, $extension) = explode('.', $file->file_name);
        $attachment->name = $file_name;
        $attachment->extension = $extension;
        $attachment->file_id = $file->_id;
        $attachment->store();
      }
    }
  }
}
