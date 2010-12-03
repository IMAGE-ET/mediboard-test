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
  
  function updateDBFields() {
  	parent::updateDBFields();
  	$this->role = "prod";
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->email;
  }
  
  function init() {
  	$this->_mail = new PHPMailer(true);
  	$this->_mail->IsSMTP();
    if($this->ssl) {
      $this->_mail->SMTPSecure = "ssl"; // sets the prefix to the server
    }
    $this->_mail->Host       = $this->host;      // SMTP server
    $this->_mail->SMTPDebug  = false;                // enables SMTP debug information (for testing)
    $this->_mail->SMTPAuth   = true;             // enable SMTP authentication
    $this->_mail->Port       = $this->port;      // set the SMTP port for the GMAIL server
    $this->_mail->Username   = $this->user;  // SMTP account username
    $this->_mail->Password   = $this->password;  // SMTP account password

    $this->_mail->SetFrom($this->email, $this->email);
    $this->_mail->AddReplyTo($this->email, $this->email);
  }
  
  function setRecipient($adresse, $name) {
    $this->_mail->AddAddress($adresse, $name);
  }
  
  function setSubject($subject) {
    $this->_mail->Subject = $subject;
  }
  
  function setBody($body) {
    $this->_mail->MsgHTML($body);
    $this->_mail->AltBody = "Pour visualiser ce message, veuillez utiliser un client mail compatible HTML"; // optional - MsgHTML will create an alternate automatically
  }
  
  function addAttachment($file_path) {
    $this->_mail->AddAttachment($file_path);
  }
  
  function addEmbeddedImage($file_path, $cid) {
    $this->_mail->AddEmbeddedImage($file_path, $cid);
  }
  
  function send() {
  	$this->_mail->send();
  }
}
?>