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
    $this->_view = $this->email;
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
}
