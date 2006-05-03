<?php /* $Id: checkstructure.php,v 1.1 2006/04/25 14:55:57 mytto Exp $ */

/**
* @package Mediboard
* @subpackage install
* @version $Revision: 1.1 $
* @author Thomas Despoix
*/

require_once("header.php");
require_once("mbdb.class.php");

$dbConfig = $dPconfig["db"]["std"];
$db = new CMbDb(
  $dbConfig["dbhost"], 
  $dbConfig["dbuser"], 
  $dbConfig["dbpass"], 
  $dbConfig["dbname"]);
$db->connect();
if (!$db->getOne("SELECT * FROM `users`")) {
  showHeader();
?>

<div class="big-error">
  Erreur : la structure de la base de données principale n'a pas été construite, il est
  donc impossible de finaliser l'installation.
  <br />Retourner à l'étape précédente pour construire la structure.
</div>

<?php
  showFooter();
  die();
}
?>
