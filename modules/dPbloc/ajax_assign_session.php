<?php 

/**
 * assign a new session var for periodical updater
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$var = CValue::get("var");
$value = CValue::get("value");

if ($var) {
  $ok = CValue::setSession($var, $value);
  if ($ok) {
    CAppUI::setMsg("Parameter-changed");
  }
}

echo CAppUI::getMsg();
