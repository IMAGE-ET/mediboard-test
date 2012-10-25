<?php 
/**
 * $Id: CHPRSpec.class.php 16236 2012-07-26 08:24:14Z phenxdesign $
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 16236 $
 */

class CHPRSpec extends CTextSpec { 
  function getSpecType() {
    return "hpr";
  }
  
  function getDBSpec() {
    return "MEDIUMTEXT";
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementTextarea($object, $params, $value, $className);
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    $value = $object->{$this->fieldName};
    
    return $value;
  }
  
  function sample(&$object, $consistent = true){
    $object->{$this->fieldName} = <<<EOD
H|^~&\|C152203.HPR||111111^MEDIBOARD ATL||ADM|||MDB^MEDIBOARD|LS1||H2.1^C|201210251522
P|1|00209272||12411338|NOM^PRENOM^^^M^||19810508|M|||||||||||||||||
A|||||||||
L|1|||
EOD;
  }
}
