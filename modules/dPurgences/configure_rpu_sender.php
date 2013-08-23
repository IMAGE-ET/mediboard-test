<?php
/**
 * $Id: configure.php 19087 2013-05-12 16:27:44Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19087 $
 */

// Le chargement des droits se fait sur le module "parent"

global $m;

$home = exec('cd ~ && pwd').("/.gnupg");
$user_apache = exec('whoami');

// Check /root is writable
$writable = is_writable($home);

$source_name = isset($source_name) ? $source_name : $m;

// Source Oscour
$source = CExchangeSource::get($source_name, "ftp", true, null, false);

// Création du template
$smarty = new CSmartyDP("modules/dPurgences");

$smarty->assign("user_apache", $user_apache);
$smarty->assign("home"       , $home);
$smarty->assign("writable"   , $writable);
$smarty->assign("source"     , $source);

$smarty->display("Config_RPU_Sender.tpl");
