<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage install
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require_once("checkconfig.php");
require_once("checkauth.php");

$dbConfigs = $dPconfig["db"];
unset($dbConfigs["ccam"]);

?>

<?php showHeader(); ?>

<h2>Test et construction initial de la base de données</h2>

<h3>Construction de la base principale</h3>

<p>
  Cette opération va créer les structures initiales des tables de la base de 
  données principale. La connexion pour la configuration 'std' doit être 
  opérationnelle pour continuer.
</p>

<form action="feed.php" name="feedBase" method="post">  
<input type="hidden" name="do" value="true" />
<table class="form">
  <tr>
    <th class="category">Construction de la base</th>
  </tr>
  <tr>
    <td class="button">
      <button class="modify" type="submit">Construire la base</button>
    </td>
  </tr>
</table>

</form>

<?php 
if (@$_POST["do"]) {
  $dbConnection = new CMbDb(
    $dbConfig["dbhost"], 
    $dbConfig["dbuser"], 
    $dbConfig["dbpass"], 
    $dbConfig["dbname"]);
  if ($dbConnection->connect()) {
    echo $dbConnection->queryDump("mediboard.sql");
  }
?>

<table class="tbl">

<tr>
  <th>Action</th>
  <th>Statut</th>
</tr>

<tr>
  <td>Construction des tables pour les 'core' modules</td>
  <td>
    <?php if (!count($dbConnection->_errors)) { ?>
    <div class="message">Construction réussie</div>
    <?php } else { ?>
    <div class="error">
      Erreurs lors de la construction
      <br />
      <?php echo nl2br(join($dbConnection->_errors, "\n")); ?>
    </div>
    <?php } ?>
  </td>
</tr>

</table>

<?php } ?>

<?php 
$dbConfig = $dbConfigs["std"];
$db = new CMbDb(
  $dbConfig["dbhost"], 
  $dbConfig["dbuser"], 
  $dbConfig["dbpass"], 
  $dbConfig["dbname"]);
$db->connect();
if ($db->getOne("SELECT * FROM `users`")) {
?>

<div class="big-warning">
  Attention, la base de données principale possède déjà une structure. La 
  reconstruire endommagerait probablement les données. 
  <br />
  Si vous désirez re-créer une structure il est nécessaire de vider initialement 
  la base avec un gestionnaire adapté (comme <a href="http://www.phpmyadmin.net/" target="_blank">PHPMyAdmin</a>).
</div>

<?php } ?>


<?php showFooter(); ?>