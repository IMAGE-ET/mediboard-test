<?php 
/**
 * Installation database structure checker
 *
 * PHP version 5.1.x+
 *  
 * @package    Mediboard
 * @subpackage Intaller
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

require_once "header.php";
require_once "CMbDb.class.php";

$dbConfig = $dPconfig["db"]["std"];
$db = new CMbDb(
  $dbConfig["dbhost"], 
  $dbConfig["dbuser"], 
  $dbConfig["dbpass"], 
  $dbConfig["dbname"]
);

$db->connect();
if (!$db->getOne("SELECT * FROM `users`")) {
  showHeader();
  // @codingStandardsIgnoreStart
?>

<div class="small-error">
  Erreur : la structure de la base de données principale n'a pas été construite, il est
  donc impossible de finaliser l'installation.
  <br />Retourner à l'étape précédente pour construire la structure.
</div>

<?php
  // @codingStandardsIgnoreStop
  require "valid.php";
  showFooter();
  die();
}
?>
