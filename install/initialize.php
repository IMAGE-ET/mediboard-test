<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage install
* @version $Revision$
* @author Thomas Despoix
*/

require_once("checkauth.php");
require_once("checkconfig.php");

// Data sources to test in the wizard
global $dPconfig;
$dbConfigs = array (
  "std"     => $dPconfig["db"]["std"]
);

require_once("mbdb.class.php");
require_once("addusers.sql.php");

?>

<?php showHeader(); ?>

<h2>Initialisation des bases de données</h2>

<p>
  Cette étape permet de créer les bases de données et les utilisateurs de base de données
  indispensables pour le fonctionnement de Mediboard. Dans un second temps, il permettra de 
  remplir ces bases avec les structures minimales.
</p>

<h3>Création des utilisateurs et des bases</h3>

<p>
  Vous êtes sur le point de créer les utilisateurs. Si vous avez des droits d'administration
  sur votre serveur de base de données, l'assistant se charge de tout créer pour vous.
  Dans le cas contraire, vous devrez fournir le code généré à un administrateur pour qu'il
  l'exécute.
</p>

<form name="createBases" action="initialize.php" method="post">

<table class="form">

  <tr>
    <th class="category" colspan="2">Avec des droits d'aministrateurs</th>
  </tr>

  <tr>
    <th><label for="adminhost" title="Nom de l'hôte">Nom de l'hôte :</label></th>
    <td><input type="text" size="40" name="adminhost" value="localhost" /></td>
  </tr>

  <tr>
    <th><label for="adminuser" title="Nom de l'utilisateur">Nom de l'administrateur :</label></th>
    <td><input type="text" size="40" name="adminuser" value="root" /></td>
  </tr>

  <tr>
    <th><label for="adminpass" title="Mot de passe de l'utililisateur'">Mot de passe de l'administrateur :</label></th>
    <td><input type="password" size="40" name="adminpass" value="" /></td>
  </tr>

  <tr>
    <td class="button" colspan="2"><input type="submit" value="Création de la base et des utilisateurs" /></td>
  </tr>

</table>

<?php 
if (@$_POST["adminhost"]) { 
  $dbConnection = new CMbDb(
    $_POST["adminhost"],
    $_POST["adminuser"],
    $_POST["adminpass"]);
    
  if ($dbConnection->connect()) {
    foreach($queries as $query) {
      $dbConnection->query($query);
    }
  }
?>

<table class="tbl">

<tr>
  <th>Action</th>
  <th>Statut</th>
</tr>

<tr>
  <td>Créations des bases et des utilisateurs</td>
  <td>
    <?php if (!count($dbConnection->_errors)) { ?>
    <div class="message">Créations réussies</div>
    <?php } else { ?>
    <div class="error">
      Erreurs lors des créations
      <br />
      <?php echo nl2br(join($dbConnection->_errors, "\n")); ?>
    </div>
    <?php } ?>
  </td>
</tr>

</table>

<?php } ?>

</form>

<form name="generateCode" action="initialize.php" method="post">

<input type="hidden" name="generate" value="true"/>
  
<table class="form">

  <tr>
    <th class="category" colspan="2">Sans des droits d'aministrateurs</th>
  </tr>

  <tr>
    <td class="button" colspan="2"><input type="submit" value="Générer le code de création des utilisateurs et des bases" /></td>
  </tr>
  
</table>

</form>

<?php if (@$_POST["generate"]) { ?>
<p>
  Merci de fournir le code suivant à un administrateur du serveur de base de
  données pour qu'il puisse l'exécuter.
</p>
<p>
  Vous <strong>ne pouvez pas </strong> continuer l'installation de Mediboard 
  tant que cette étape n'est effectuée.
</p>

<textarea cols="50" rows="10"><?php echo join($queries, "\n\n"); ?></textarea>
<?php } ?>

<h3>Tests de connexion</h3>

<div class="big-info">
  Désormais, seule la source de données principale <tt>std</tt> est créée et testée dans l'assistant d'installation.
  <br />
  L'administration des bases de données secondaires est déléguée à <strong>la configuration de chacun des modules correspondant.</strong>.
</div>

<table class="tbl">
  <tr>
    <th>Configuration</th>
    <th>Test de connectivité</th>
  </tr>
  <?php 
  foreach($dbConfigs as $dbConfigName => $dbConfig) { 
    $dbConnection = new CMbDb(
      $dbConfig["dbhost"], 
      $dbConfig["dbuser"], 
      $dbConfig["dbpass"], 
      $dbConfig["dbname"]);
    $dbConnection->connect();
  ?>
  <tr>
    <td><?php echo $dbConfigName; ?>
    </td>
    <td>
    
    <?php if (!count($dbConnection->_errors)) { ?>
      <div class="message">Connexion réussie</div>
    <?php } else { ?>
      <div class="error">
        Echec de connexion
        <br />
        <?php echo nl2br(join($dbConnection->_errors, "\n")); ?>
      </div>
    <?php } ?>

    </td>
  </tr>
  <?php } ?>
  
</table>

<?php showFooter(); ?>
