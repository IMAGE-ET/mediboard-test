<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * HPrim string
 */
class CHPRSpec extends CTextSpec {
  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "hpr";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec() {
    return "MEDIUMTEXT";
  }

  /**
   * @see parent::getFormHtmlElement()
   */
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementTextarea($object, $params, $value, $className);
  }

  /**
   * @see parent::getValue()
   */
  function getValue($object, $smarty = null, $params = array()) {
    $value = $object->{$this->fieldName};

    if (isset($params["advanced"]) && $params["advanced"]) {
      $message = new CHPrim21Message();
      $message->parse($value);
      return $message->flatten(true);
    }

    return CHPrim21Message::highlight($value);
  }

  /**
   * @see parent::sample()
   */
  function sample(&$object, $consistent = true){
    $object->{$this->fieldName} = <<<HPR
H|^~&\|C152203.HPR||111111^MEDIBOARD ATL||ADM|||MDB^MEDIBOARD|LS1||H2.1^C|201210251522
P|1|00209272||12411338|NOM^PRENOM^^^M^||19810508|M|||||||||||||||||
A|||||||||
L|1|||
HPR;
  }
}
