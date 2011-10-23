<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireLibraryFile("phpmailer/class.phpmailer");
CAppUI::requireLibraryFile("phpmailer/class.smtp");

class CSourceSMTP extends CExchangeSource {
  // DB Table key
  var $source_smtp_id = null;
  
  // DB Fields
  var $port     = null;
  var $email    = null;
  var $ssl      = null;
  
  var $_mail    = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_smtp';
    $spec->key   = 'source_smtp_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["port"]     = "num default|25";
    $specs["email"]    = "email";
    $specs["ssl"]      = "bool";
    $specs["password"] = "password";
    
    return $specs;
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
    if($this->ssl) {
      $this->_mail->SMTPSecure = "ssl"; // sets the prefix to the server
    }
    $this->_mail->Host       = $this->host;      // SMTP server
    $this->_mail->SMTPDebug  = false;            // enables SMTP debug information (for testing)
    $this->_mail->SMTPAuth   = true;             // enable SMTP authentication
    $this->_mail->Port       = $this->port;      // set the SMTP port for the GMAIL server
    $this->_mail->Username   = $this->user;      // SMTP account username
    $this->_mail->Password   = $this->password;  // SMTP account password

    $this->_mail->SetFrom($this->email, '', 0);
  }
  
  /**
   * Set a supposably unique to-address
   * 
   * @param $adress E-mail address
   * @param $name   Display name
   * @return bool   Job done
   */
  function setRecipient($address, $name = '') {
    return $this->_mail->AddAddress($address, $name);
  }
  
  /**
   * Add a to-address
   * 
   * @param $adress E-mail address
   * @param $name   Display name
   * @return bool   Job done
   */
  function addTo($address, $name = '') {
    return $this->_mail->AddAddress($address, $name);
  }
  
  /**
   * Add a cc-address
   * 
   * @param $adress E-mail address
   * @param $name   Display name
   * @return bool   Job done
   */
  function addCc($address, $name = '') {
    return $this->_mail->AddCC($address, $name);
  }

  /**
   * Add a bcc-address
   * 
   * @param $adress E-mail address
   * @param $name   Display name
   * @return bool   Job done
   */
  function addBcc($address, $name = '') {
    return $this->_mail->AddBCC($address, $name);
  }
  
  /**
   * Add a replyto-address
   * 
   * @param $adress E-mail address
   * @param $name   Display name
   * @return bool   Job done
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
    $this->_mail->AddAttachment($file_path, $name);
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
?>